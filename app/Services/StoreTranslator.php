<?php

namespace App\Services;

use App\Models\BookingService;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Item;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;

/**
 * Translates a store's own content (services, products, categories, FAQs, store description and
 * the AI design texts) into every website language with AI.
 *
 * Work is done in small steps — one AI call per step(), a few records at a time — so the admin
 * page can show progress and no single request runs long enough to hit a server timeout.
 * The artisan command `store:translate` simply calls step() until nothing is left.
 */
class StoreTranslator
{
    /** Website languages: code => name the AI understands. */
    public const LANGS = ['en' => 'English', 'ar' => 'Arabic', 'de' => 'German', 'fr' => 'French'];

    /** Content types, in the order they are translated. */
    public const TYPES = [
        'store'    => Settings::class,
        'category' => Category::class,
        'service'  => BookingService::class,
        'item'     => Item::class,
        'faq'      => Faq::class,
    ];

    /** Records per AI call (service descriptions are long; four languages each). */
    private const CHUNK = 6;

    /** Placeholder text new stores start with — never worth translating. */
    private const PLACEHOLDERS = ['your description', 'description', '-'];

    // ---------------------------------------------------------------------------------------
    // What is there / what is left
    // ---------------------------------------------------------------------------------------

    /** All translatable records of one type for this store. */
    public static function records(string $type, int $vid)
    {
        $cls = self::TYPES[$type];
        $q = $cls::where('vendor_id', $vid);
        if ($type === 'category') {
            $q->where('is_deleted', 2);
        }
        if ($type === 'service' || $type === 'item' || $type === 'faq' || $type === 'category') {
            $q->orderBy('reorder_id');
        }

        return $q->get()->filter(function ($r) use ($type) {
            $src = $r->translatableSource();
            if ($type === 'store' && in_array(mb_strtolower($src['description'] ?? ''), self::PLACEHOLDERS, true)) {
                return false;
            }

            return !empty($src);
        })->values();
    }

    /** Records still waiting for a translation, per type, plus the design texts. */
    public static function pending(int $vid): array
    {
        $out = ['total' => 0];
        foreach (array_keys(self::TYPES) as $type) {
            $n = self::records($type, $vid)->filter(fn($r) => !$r->translationIsCurrent())->count();
            $out[$type] = $n;
            $out['total'] += $n;
        }
        $out['design'] = self::designPending($vid) ? 1 : 0;
        $out['total'] += $out['design'];

        return $out;
    }

    // ---------------------------------------------------------------------------------------
    // Doing the work
    // ---------------------------------------------------------------------------------------

    /**
     * Translate the next batch for this store.
     *
     * @return array{success: bool, done: int, remaining: int, error?: string}
     */
    public static function step(int $vid): array
    {
        if (!AiAssistant::enabled()) {
            return ['success' => false, 'done' => 0, 'remaining' => self::pending($vid)['total'], 'error' => trans('messages.assistant_unavailable')];
        }
        @set_time_limit(240);

        // 1) The AI design texts (one call for the whole design).
        if (self::designPending($vid)) {
            $ok = self::translateDesigns($vid);
            if (!$ok) {
                return ['success' => false, 'done' => 0, 'remaining' => self::pending($vid)['total'], 'error' => trans('messages.translate_failed')];
            }

            return ['success' => true, 'done' => 1, 'remaining' => self::pending($vid)['total']];
        }

        // 2) The next few records, whatever their type.
        $batch = [];
        foreach (array_keys(self::TYPES) as $type) {
            foreach (self::records($type, $vid) as $r) {
                if (!$r->translationIsCurrent()) {
                    $batch[$type . ':' . $r->id] = $r;
                    if (count($batch) >= self::CHUNK) {
                        break 2;
                    }
                }
            }
        }
        if (!$batch) {
            return ['success' => true, 'done' => 0, 'remaining' => 0];
        }

        $entries = [];
        foreach ($batch as $key => $r) {
            $entries[$key] = $r->translatableSource();
        }
        $result = self::askRecords($entries, self::glossary($vid));
        if ($result === null) {
            return ['success' => false, 'done' => 0, 'remaining' => self::pending($vid)['total'], 'error' => trans('messages.translate_failed')];
        }

        $done = 0;
        foreach ($batch as $key => $r) {
            $t = ['_src' => $r->sourceHash()];
            $res = $result[$key] ?? null;
            if (is_array($res)) {
                foreach (array_keys(self::LANGS) as $lang) {
                    if (!is_array($res[$lang] ?? null)) {
                        continue;
                    }
                    $fields = [];
                    foreach ($r->translatableSource() as $f => $orig) {
                        $v = $res[$lang][$f] ?? null;
                        if (is_string($v) && trim($v) !== '') {
                            $fields[$f] = self::cleanText($v, $f);
                        }
                    }
                    if ($fields) {
                        $t[$lang] = $fields;
                    }
                }
                $done++;
            } else {
                // Not returned by the AI: marked done (shows the original) so the loop cannot
                // spin on it; "translate everything again" retries it.
                $t['_missing'] = 1;
            }
            $r::where('id', $r->id)->update(['translations' => json_encode($t, JSON_UNESCAPED_UNICODE)]);
        }

        return ['success' => true, 'done' => $done, 'remaining' => self::pending($vid)['total']];
    }

    /** Stores with new / edited text in this request, translated once the response is sent. */
    private static array $queued = [];

    public static function afterResponse(int $vid): void
    {
        if ($vid <= 1 || isset(self::$queued[$vid]) || !AiAssistant::enabled()) {
            return;
        }
        $first = !self::$queued;
        self::$queued[$vid] = true;
        if (!$first) {
            return;
        }
        app()->terminating(function () {
            @ignore_user_abort(true);
            foreach (array_keys(self::$queued) as $v) {
                try {
                    // A handful of batches covers normal edits; a big import finishes on the
                    // Languages page or with `php artisan store:translate`.
                    for ($i = 0; $i < 6; $i++) {
                        $r = self::step($v);
                        if (empty($r['success']) || $r['remaining'] <= 0 || $r['done'] === 0) {
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('StoreTranslator: background translation failed for vendor ' . $v . ': ' . $e->getMessage());
                }
            }
            self::$queued = [];
        });
    }

    /** Forget every translation of this store so the next run translates it all again. */
    public static function reset(int $vid): void
    {
        foreach (self::TYPES as $cls) {
            $cls::where('vendor_id', $vid)->update(['translations' => null]);
        }
        $settings = Settings::where('vendor_id', $vid)->first();
        if ($settings) {
            foreach (['ai_design', 'ai_design_draft'] as $col) {
                $d = json_decode((string) $settings->{$col}, true);
                if (is_array($d) && isset($d['copy'])) {
                    unset($d['copy']['de'], $d['copy']['fr']);
                    Settings::where('id', $settings->id)->update([$col => json_encode($d, JSON_UNESCAPED_UNICODE)]);
                }
            }
        }
    }

    /** Save the owner's own corrections for one record ($values = [lang => [field => text]]). */
    public static function saveManual($record, array $values): void
    {
        $t = $record->translationIsCurrent() ? (array) $record->translations : ['_src' => $record->sourceHash()];
        $t['_src'] = $record->sourceHash();
        unset($t['_missing']);
        foreach (array_keys(self::LANGS) as $lang) {
            foreach ($record->translatableSource() as $f => $orig) {
                if (!array_key_exists($f, $values[$lang] ?? [])) {
                    continue;
                }
                $v = trim((string) $values[$lang][$f]);
                if ($v === '' || $v === $orig) {
                    unset($t[$lang][$f]);
                } else {
                    $t[$lang][$f] = self::cleanText($v, $f);
                }
            }
            if (isset($t[$lang]) && empty($t[$lang])) {
                unset($t[$lang]);
            }
        }
        $record::where('id', $record->id)->update(['translations' => json_encode($t, JSON_UNESCAPED_UNICODE)]);
    }

    // ---------------------------------------------------------------------------------------
    // AI design texts
    // ---------------------------------------------------------------------------------------

    private static function designPending(int $vid): bool
    {
        $settings = Settings::where('vendor_id', $vid)->first(['ai_design', 'ai_design_draft']);
        foreach (['ai_design', 'ai_design_draft'] as $col) {
            $d = json_decode((string) optional($settings)->{$col}, true);
            if (is_array($d) && self::missingCopyLangs($d)) {
                return true;
            }
        }

        return false;
    }

    /** Languages a design has no texts for yet (empty when it has no texts at all). */
    private static function missingCopyLangs(array $design): array
    {
        $copy = (array) ($design['copy'] ?? []);
        if (empty($copy['en']) && empty($copy['ar'])) {
            return [];
        }

        return array_values(array_filter(array_keys(self::LANGS), fn($l) => empty($copy[$l])));
    }

    /** Add the missing languages to the published design and the draft. */
    private static function translateDesigns(int $vid): bool
    {
        $settings = Settings::where('vendor_id', $vid)->first();
        if (!$settings) {
            return true;
        }
        $done = [];
        foreach (['ai_design', 'ai_design_draft'] as $col) {
            $d = json_decode((string) $settings->{$col}, true);
            if (!is_array($d) || !self::missingCopyLangs($d)) {
                continue;
            }
            // The draft is usually a copy of the published design: reuse that translation.
            $key = md5(json_encode($d['copy'] ?? []));
            $new = $done[$key] ?? self::translateDesign($d, $vid);
            if ($new === null) {
                return false;
            }
            $done[$key] = $new;
            Settings::where('id', $settings->id)->update([$col => json_encode($new, JSON_UNESCAPED_UNICODE)]);
        }

        return true;
    }

    /** The design with texts in every language (null when the AI call failed). */
    public static function translateDesign(array $design, int $vid): ?array
    {
        $missing = self::missingCopyLangs($design);
        if (!$missing) {
            return $design;
        }
        $srcLang = !empty($design['copy']['en']) ? 'en' : 'ar';
        $names = implode(', ', array_map(fn($l) => self::LANGS[$l] . ' ("' . $l . '")', $missing));

        $instructions = <<<TXT
You translate the texts of a business website. The input is the website's texts in {$srcLang}.
Translate them into: {$names}. Keep exactly the same JSON keys and list structure, translate only
the values. Keep the meaning, tone and length. Keep brand and business names, numbers, prices
and currency codes unchanged. Arabic: clear Modern Standard Arabic for Gulf customers.
Return ONLY a JSON object: {"<language code>": { same keys as the input }, ...}
TXT;
        [$data, $r] = self::askJson($instructions, json_encode($design['copy'][$srcLang], JSON_UNESCAPED_UNICODE), 9000);
        if (!is_array($data)) {
            Log::warning('StoreTranslator: design translation failed for vendor ' . $vid . ': ' . ($r['error'] ?? 'bad JSON'));

            return null;
        }
        foreach ($missing as $lang) {
            if (is_array($data[$lang] ?? null)) {
                $design['copy'][$lang] = $data[$lang];
            }
        }

        return StoreDesign::normalize($design, $vid);
    }

    // ---------------------------------------------------------------------------------------
    // AI call for records
    // ---------------------------------------------------------------------------------------

    /**
     * @param array $entries  key => [field => original text]
     * @return array|null     key => [lang => [field => text]]
     */
    private static function askRecords(array $entries, array $glossary): ?array
    {
        $langs = implode(', ', array_map(fn($c, $n) => "$n (\"$c\")", array_keys(self::LANGS), self::LANGS));
        $instructions = <<<TXT
You translate the content of a business website (service, product and category names,
descriptions, FAQs) for its customers. Website languages: {$langs}.

For EVERY entry in "items", give EVERY field in all four languages (en, ar, de, fr), with the
same field names. Fields of one entry may be written in different languages; a field that is
already in a language is returned unchanged for that language.
- Keep the meaning and the line breaks (\\n). Do not add or remove information.
- TRANSLATE every name too: service, product, dish, room and section names are ordinary words
  ("Knee Injuries" → ar "إصابات الركبة", de "Knieverletzungen", fr "Blessures du genou";
  "Deluxe Room" → ar "غرفة ديلوكس"). Never return a text unchanged in another language.
- Currencies: in en / de / fr write the code (SAR, BHD, USD…); in Arabic write it the Arabic
  way (ريال، دينار، دولار). Never leave Arabic words in en / de / fr texts or Latin words that
  have an Arabic equivalent in Arabic texts.
- Keep unchanged only: numbers, prices, sizes and units, phone
  numbers, emails, URLs, people's names and registered brand names (e.g. "Dr. Ahmed",
  "Coca-Cola") — the words around them are still translated ("Dr. Ahmed — Cardiology" →
  ar "د. أحمد — أمراض القلب").
- Arabic: clear Modern Standard Arabic for Gulf customers. German and French: natural, as a
  local business would write it.
- Use the "glossary" translations whenever those exact terms appear.

Return ONLY JSON: {"items": {"<key>": {"en": {...}, "ar": {...}, "de": {...}, "fr": {...}}}}
TXT;
        $input = json_encode(['glossary' => $glossary ?: new \stdClass(), 'items' => $entries], JSON_UNESCAPED_UNICODE);
        [$data, $r] = self::askJson($instructions, $input, 12000);
        if (!is_array($data) || !is_array($data['items'] ?? null)) {
            Log::warning('StoreTranslator: record translation failed: ' . ($r['error'] ?? 'bad JSON'));

            return null;
        }

        return $data['items'];
    }

    /** One AI call in strict JSON mode, retried once when the answer is not usable JSON. */
    private static function askJson(string $instructions, string $input, int $maxTokens): array
    {
        $ai = app(AiAssistant::class);
        $r = [];
        for ($try = 0; $try < 2; $try++) {
            // JSON mode needs the word "JSON" in the input itself.
            $r = $ai->run($instructions, "Input (JSON):\n" . $input, $maxTokens, 170, true);
            $data = !empty($r['success']) ? $ai->parseJson((string) $r['text']) : null;
            if (is_array($data)) {
                return [$data, $r];
            }
        }

        return [null, $r];
    }

    /** Service sections already translated, so every service in a section gets the same name. */
    private static function glossary(int $vid): array
    {
        $out = [];
        foreach (BookingService::where('vendor_id', $vid)->get(['id', 'name', 'category', 'description', 'translations']) as $s) {
            $cat = trim((string) $s->category);
            if ($cat === '' || isset($out[$cat]) || !$s->translationIsCurrent()) {
                continue;
            }
            $tr = [];
            foreach (array_keys(self::LANGS) as $lang) {
                if (!empty($s->translations[$lang]['category'])) {
                    $tr[$lang] = $s->translations[$lang]['category'];
                }
            }
            if ($tr) {
                $out[$cat] = $tr;
            }
        }

        return array_slice($out, 0, 40, true);
    }

    private static function cleanText(string $v, string $field): string
    {
        $v = trim(strip_tags($v));
        $max = in_array($field, ['description', 'answer'], true) ? 5000 : 255;

        return mb_substr($v, 0, $max);
    }
}
