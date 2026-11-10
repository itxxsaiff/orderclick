<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Modular AI content assistant (OpenAI).
 *
 * One place for every AI call in Order Click. Add new capabilities as methods
 * or new $action cases in assist() — controllers/views only ever call this class,
 * so swapping model/provider later is a one-file change.
 */
class AiAssistant
{
    /** Is AI configured (key present)? Views use this to show/hide the button. */
    public static function enabled(): bool
    {
        return !empty(config('services.openai.key'));
    }

    /**
     * High-level entry point. Maps a friendly action to a prompt and runs it.
     *
     * @param string $action  improve | translate | grammar | professional | seo | generate
     * @param string $text    the user's current content (or a topic, for "generate")
     * @param array  $opts    ['field' => 'product name', 'target_lang' => 'English', 'context' => '...']
     */
    public function assist(string $action, string $text, array $opts = []): array
    {
        $field = trim((string) ($opts['field'] ?? 'content'));
        $target = trim((string) ($opts['target_lang'] ?? 'English'));
        $context = trim((string) ($opts['context'] ?? ''));
        $text = trim($text);

        // Guard: nothing to work with (except generate, which can start from a field/context).
        if ($text === '' && $action !== 'generate') {
            return ['success' => false, 'error' => 'Please write something first.'];
        }

        $base = "You are a professional copywriter for an online store platform. "
            . "Write natural, appealing, correct copy. Keep it concise and suitable for a {$field}. "
            . "Return ONLY the resulting text — no quotes, no explanations, no labels, no markdown.";

        switch ($action) {
            case 'improve':
                $instructions = "{$base} Improve and polish the following {$field}. Keep the SAME language as the input. Fix grammar and flow, make it clearer and more appealing, keep the original meaning.";
                $input = $text;
                break;

            case 'grammar':
                $instructions = "{$base} Correct spelling and grammar only. Keep the SAME language and meaning; do not rewrite the style.";
                $input = $text;
                break;

            case 'professional':
                $instructions = "{$base} Rewrite the following {$field} in a professional, trustworthy, marketing-ready tone. Keep the SAME language.";
                $input = $text;
                break;

            case 'translate':
                $instructions = "{$base} Translate the following {$field} into {$target}. Keep the tone and meaning; localise naturally rather than word-for-word.";
                $input = $text;
                break;

            case 'seo':
                $instructions = "{$base} Rewrite the following {$field} to be SEO-friendly: include relevant keywords naturally, stay accurate, keep the SAME language. Keep it concise.";
                $input = $text;
                break;

            case 'generate':
                $instructions = "{$base} Generate a professional {$field}"
                    . ($target ? " in {$target}" : '') . ". Base it on this: " . ($context ?: $text);
                $input = $context ?: $text;
                break;

            default:
                return ['success' => false, 'error' => 'Unknown AI action.'];
        }

        return $this->run($instructions, $input);
    }

    /**
     * Generate a full starter store (description, colour, categories, products/services) as structured data.
     * Returns ['success'=>true, 'data'=>[...]] or ['success'=>false,'error'=>...].
     *
     * $system (orders | booking | service) decides what is generated — products, bookable sessions or
     * service packages. Older callers that pass only a business type still work.
     * $activity is the merchant's activity/specialization ("Fitness & Sports / Physical Therapy"), for context.
     */
    public function buildStore(string $businessType, string $storeName, string $offerings, string $lang = 'English', string $categories = '', string $system = '', string $activity = ''): array
    {
        if (!in_array($system, ['orders', 'booking', 'service'], true)) {
            $system = in_array($businessType, ['booking', 'clinic', 'salon'], true) ? 'booking'
                : ($businessType === 'service' ? 'service' : 'orders');
        }

        if ($system === 'booking') {
            $kind = 'appointment / booking business (customers book a time slot)';
            $unit = 'bookable services or sessions';
            $schema = '{"description":"1-2 sentence business description","primary_color":"#1f9d55","categories":["Type A","Type B","Type C"],"items":[{"name":"Session name","category":"Type A","price":10,"duration":"45 min","description":"one line"}]}';
            $extra = 'Each item is something a customer books (a session, appointment, class or rental slot) with a realistic duration such as "30 min" or "1 hour". ';
        } elseif ($system === 'service') {
            $kind = 'service provider (customers send a service request or ask for a quote)';
            $unit = 'service packages';
            $schema = '{"description":"1-2 sentence business description","primary_color":"#1f9d55","categories":["Type A","Type B","Type C"],"items":[{"name":"Service package name","category":"Type A","price":25,"description":"one line about what is included"}]}';
            $extra = 'Each item is a service package or job the provider offers (e.g. a visit, a fixed-price job, an hourly service or a project package) with a realistic starting price. ';
        } else {
            $kind = 'online store selling products';
            $unit = 'products';
            $schema = '{"description":"1-2 sentence store description","primary_color":"#1f9d55","categories":["Category A","Category B","Category C"],"items":[{"name":"Product name","category":"Category A","price":3.5,"description":"one appealing line"}]}';
            $extra = '';
        }
        $about = $activity !== '' ? $activity : $businessType;

        $catRule = $categories !== ''
            ? "Use EXACTLY these categories (in this order): {$categories}. Add 3-4 {$unit} to each (at most 20 items in total)."
            : "Choose 3-4 sensible categories; 3-4 {$unit} per category (9-14 total).";

        $instructions = "You are setting up the website of a {$kind} named \"{$storeName}\". Business activity: {$about}. "
            . "Generate realistic, appealing starter content. Return ONLY valid minified JSON (no markdown, no code fences) matching EXACTLY this shape: {$schema}. "
            . "The list MUST be under the key \"items\". {$extra}"
            . "Rules: {$catRule} If the merchant listed specific items with prices, include them with those prices; otherwise invent realistic ones. "
            . "Prices are plain numbers; primary_color is a tasteful hex suiting the business; write everything in {$lang}; keep names short and each description to one line.";

        $input = "Business activity: {$about}\nStore name: {$storeName}"
            . ($categories !== '' ? "\nCategories: {$categories}" : '')
            . "\nWhat they offer: " . ($offerings ?: $about);

        // One retry: a reply cut off mid-JSON (long Arabic lists) or wrapped in prose is not worth an error.
        $lastText = '';
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $res = $this->run($instructions, $input, 6000, 60);
            if (!$res['success']) {
                if ($attempt === 2) {
                    return $res;
                }
                continue;
            }
            $lastText = $res['text'];
            $json = $this->parseJson($res['text']);
            if (is_array($json)) {
                // Models sometimes name the list after the business ("services", "packages"...).
                if (empty($json['items'])) {
                    foreach (['services', 'products', 'packages', 'sessions', 'offerings'] as $alt) {
                        if (!empty($json[$alt]) && is_array($json[$alt])) {
                            $json['items'] = $json[$alt];
                            break;
                        }
                    }
                }
                if (!empty($json['items']) && is_array($json['items'])) {
                    return ['success' => true, 'data' => $json];
                }
            }
        }

        \Log::warning('AI buildStore: unusable reply', [
            'system' => $system, 'activity' => $about, 'lang' => $lang,
            'length' => mb_strlen($lastText), 'start' => mb_substr($lastText, 0, 300),
        ]);

        return ['success' => false, 'error' => 'AI could not generate store content. Please try again.'];
    }

    /**
     * The three systems in plain words, for prompts: what the business is and what its list holds.
     */
    private static function systemWords(string $system): array
    {
        if ($system === 'booking') {
            return ['an appointment / booking business (customers book a time slot)', 'bookable services or sessions',
                '{"name":"Session name","price":10,"duration":"45 min","description":"one line"}',
                'Each item is something a customer books (a session, appointment, class or rental slot). "duration" is the length of ONE visit or session, such as "30 min" or "1 hour" (never weeks or months, also for packages and monthly plans). '];
        }
        if ($system === 'service') {
            return ['a service provider (customers send a service request or ask for a quote)', 'service packages',
                '{"name":"Service package name","price":25,"description":"one line about what is included"}',
                'Each item is a service package or job the provider offers (a visit, a fixed-price job, an hourly service or a project package) with a realistic starting price. '];
        }

        return ['an online store selling products', 'products',
            '{"name":"Product name","price":3.5,"description":"one appealing line"}', ''];
    }

    /**
     * Step 1 of building a store: description, About text, colour and the category list. Small and
     * fast; the items are generated per category afterwards (buildItems) so no single AI call is long
     * enough to time out, however big the merchant's list is.
     */
    public function planStore(string $system, string $about, string $storeName, string $offerings, string $categories, string $lang): array
    {
        [$kind, $unit] = self::systemWords($system);
        $catRule = $categories !== ''
            ? "Use EXACTLY these categories, in this order: {$categories}."
            : ($offerings !== ''
                ? "Group the merchant's list into 2-8 clear categories that together cover EVERY item in it."
                : "Choose 3-5 sensible categories for this business.");

        // A real list (one item per line) is split between the categories here, once, so every item
        // ends up in exactly one category when the items are generated per category.
        $lines = self::offeringLines($offerings);
        $assign = count($lines) >= 3;
        $numbered = $assign
            ? implode("\n", array_map(fn($l, $i) => ($i + 1) . '. ' . $l, $lines, array_keys($lines)))
            : mb_substr($offerings, 0, 6000);

        $instructions = "You are setting up the website of {$kind} named \"{$storeName}\". Business activity: {$about}. "
            . "Return ONLY valid minified JSON (no markdown) of EXACTLY this shape: "
            . '{"description":"1-2 sentence description","about":"an About-us text of 3-5 sentences","primary_color":"#1f9d55","categories":["A","B"]'
            . ($assign ? ',"assign":{"1":"A","2":"B"}' : '') . '}. '
            . "{$catRule} Categories group the {$unit}. Never invent facts (years, awards, ratings, addresses). "
            . ($assign ? "In \"assign\", put EVERY numbered line of the merchant's list into exactly one category (the best fit), using the category names exactly. " : '')
            . "primary_color is a tasteful hex that suits the business. Write everything in {$lang} (category names too).";
        $input = "Business activity: {$about}\nName: {$storeName}"
            . ($categories !== '' ? "\nCategories: {$categories}" : '')
            . "\nWhat they offer:\n" . ($numbered ?: $about);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $res = $this->run($instructions, $input, 2500, 45);
            if (!$res['success']) {
                if ($attempt === 2) {
                    return $res;
                }
                continue;
            }
            $json = $this->parseJson($res['text']);
            if (is_array($json) && !empty($json['categories']) && is_array($json['categories'])) {
                $json['categories'] = array_values(array_slice(array_filter(array_map(
                    fn($c) => mb_substr(trim((string) $c), 0, 60), $json['categories']
                )), 0, 8));
                if ($json['categories']) {
                    // groups: category => the merchant's own lines for it (only for a real list).
                    $json['groups'] = null;
                    if ($assign && is_array($json['assign'] ?? null)) {
                        $groups = array_fill_keys($json['categories'], []);
                        $lower = [];
                        foreach ($json['categories'] as $c) {
                            $lower[mb_strtolower($c)] = $c;
                        }
                        foreach ($lines as $i => $line) {
                            $cat = $lower[mb_strtolower(trim((string) ($json['assign'][(string) ($i + 1)] ?? '')))] ?? $json['categories'][0];
                            $groups[$cat][] = $line;
                        }
                        $json['groups'] = $groups;
                    }
                    unset($json['assign']);
                    return ['success' => true, 'data' => $json];
                }
            }
        }
        \Log::warning('AI planStore: unusable reply', ['system' => $system, 'activity' => $about]);

        return ['success' => false, 'error' => 'AI could not generate store content. Please try again.'];
    }

    /** The merchant's list as separate non-empty lines (bullets and numbering removed). */
    private static function offeringLines(string $offerings): array
    {
        $lines = [];
        foreach (preg_split('/\R/u', $offerings) as $line) {
            $line = trim(preg_replace('/^\s*(?:[-*•·]|\d+[.)])\s*/u', '', $line));
            if ($line !== '') {
                $lines[] = mb_substr($line, 0, 200);
            }
        }

        return array_slice($lines, 0, 120);
    }

    /**
     * Step 2: the items of ONE category. With $assigned the merchant's lines were already sorted into
     * this category by planStore, so all of them are used (and nothing is invented unless it is empty).
     */
    public function buildItems(string $system, string $about, string $storeName, string $category, array $allCategories, string $offerings, string $lang, bool $assigned = false): array
    {
        [$kind, $unit, $shape, $extra] = self::systemWords($system);
        $others = implode(', ', array_filter($allCategories, fn($c) => $c !== $category));

        if ($assigned) {
            $rule = trim($offerings) !== ''
                ? "These are the merchant's own items for this category: include EVERY one of them, each with its price exactly as given "
                    . "(a price per session or per package stays as written; put details such as 'monthly plan, 26 sessions' in the description). Do not add other items. "
                : "Create 3-4 realistic {$unit} for this category. ";
            $instructions = "You are filling the website of {$kind} named \"{$storeName}\" (activity: {$about}). "
                . "Return ONLY valid minified JSON (no markdown) of EXACTLY this shape: {\"items\":[{$shape}]}. "
                . "Category: \"{$category}\". {$rule}At most 30 items. {$extra}"
                . "Prices are plain numbers. Write names and descriptions in {$lang}; keep names short and each description to one line.";
            $input = "Category: {$category}\n" . (trim($offerings) !== '' ? "Items:\n" . mb_substr($offerings, 0, 6000) : "Business activity: {$about}");

            return $this->itemsCall($instructions, $input, $system, $category, 30);
        }

        $instructions = "You are filling the website of {$kind} named \"{$storeName}\" (activity: {$about}). "
            . "Return ONLY valid minified JSON (no markdown) of EXACTLY this shape: {\"items\":[{$shape}]}. "
            . "List the {$unit} of the category \"{$category}\" only" . ($others !== '' ? " (the other categories are: {$others})" : '') . ". "
            . "Every item of the merchant's list below belongs to exactly ONE category — the best fit. Return ONLY the items whose best fit is \"{$category}\" "
            . "(never items that fit another category better), each with its price exactly as given "
            . "(a price per session or per package stays as written; put details such as 'monthly plan, 26 sessions' in the description). "
            . "If the list has nothing for this category, create 3-4 realistic {$unit}. At most 15 items. {$extra}"
            . "Prices are plain numbers. Write names and descriptions in {$lang}; keep names short and each description to one line.";
        $input = "Category: {$category}\nWhat the merchant offers:\n" . (mb_substr($offerings, 0, 6000) ?: $about);

        return $this->itemsCall($instructions, $input, $system, $category, 15);
    }

    private function itemsCall(string $instructions, string $input, string $system, string $category, int $max): array
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $res = $this->run($instructions, $input, 4000, 45);
            if (!$res['success']) {
                if ($attempt === 2) {
                    return $res;
                }
                continue;
            }
            $json = $this->parseJson($res['text']);
            $items = is_array($json) ? ($json['items'] ?? $json['services'] ?? $json['products'] ?? $json['packages'] ?? null) : null;
            if (is_array($items) && $items) {
                return ['success' => true, 'items' => array_slice($items, 0, $max)];
            }
        }
        \Log::warning('AI buildItems: unusable reply', ['system' => $system, 'category' => $category]);

        return ['success' => false, 'error' => 'AI could not generate store content. Please try again.'];
    }

    /**
     * Read an uploaded file (menu / product list photo or PDF) and extract the offerings.
     * Also judges whether the file is RELEVANT and CLEAR, so the UI can guide the user.
     *
     * Returns ['success'=>true,'data'=>['relevant'=>bool,'clear'=>bool,'reason'=>string,'offerings'=>string,'categories'=>string]]
     * or ['success'=>false,'error'=>string].
     */
    public function extractFromFile(string $dataUrl, string $mime, string $businessType, string $lang = 'English'): array
    {
        $isPdf = stripos($mime, 'pdf') !== false;

        $instructions = "You help set up an online store for a '{$businessType}' business. "
            . "The user uploaded a file that should be a menu, product list, catalogue, service list or price list (including session or package prices). "
            . "STEP 1 — judge the file: is it RELEVANT (an actual menu/products/prices for this kind of business, not a selfie, landscape, screenshot of something unrelated, or a blank/random document)? "
            . "Is it CLEAR enough to read the text and prices (not too blurry, dark, tiny or cropped)? "
            . "STEP 2 — if relevant AND clear, extract every item you can see. "
            . "Return ONLY valid minified JSON (no markdown, no code fences) matching EXACTLY this shape: "
            . '{"relevant":true,"clear":true,"reason":"","offerings":"","categories":""}. '
            . "Rules: 'offerings' = a plain list with ONE ITEM PER LINE separated by real newlines, each line like 'Item name - price' "
            . "(keep prices/numbers exactly as shown, omit the price if there is none; do NOT use angle brackets, placeholders or empty dashes). "
            . "'categories' = a short comma-separated list of the main sections you saw. "
            . "Write 'offerings', 'categories' and 'reason' in {$lang}. "
            . "If NOT relevant, set relevant=false and put a short friendly reason (what the file looks like instead). "
            . "If relevant but NOT clear, set clear=false and a short friendly reason. Never invent items that are not in the file.";

        $content = [['type' => 'input_text', 'text' => 'Read this file and extract the menu / products.']];
        if ($isPdf) {
            $content[] = ['type' => 'input_file', 'filename' => 'upload.pdf', 'file_data' => $dataUrl];
        } else {
            $content[] = ['type' => 'input_image', 'image_url' => $dataUrl];
        }

        $res = $this->runMultimodal($instructions, $content, 3000);
        if (!$res['success']) {
            return $res;
        }
        $data = $this->parseJson($res['text']);
        if (!is_array($data)) {
            return ['success' => false, 'error' => 'Could not read that file. Please try a clearer photo, or type your items manually.'];
        }
        return ['success' => true, 'data' => $data];
    }

    /**
     * OpenAI call with multimodal input (text + image/file content items).
     * Mirrors run() but sends an input message array instead of a plain string.
     */
    public function runMultimodal(string $instructions, array $content, int $maxTokens = 2000, ?string $model = null): array
    {
        $key = config('services.openai.key');
        if (empty($key)) {
            return ['success' => false, 'error' => 'AI is not configured yet.'];
        }
        $model = $model ?: config('services.openai.model');
        $payload = [
            'model' => $model,
            'instructions' => $instructions,
            'input' => [['role' => 'user', 'content' => $content]],
            'max_output_tokens' => $maxTokens,
        ];
        // Only reasoning models (gpt-5*, o*) accept this; others reject the parameter.
        if (preg_match('/^(gpt-5|o\d)/', $model)) {
            $payload['reasoning'] = ['effort' => 'minimal'];
        }
        try {
            $response = Http::withToken($key)
                ->timeout(150)
                ->acceptJson()
                ->post(config('services.openai.endpoint'), $payload);

            if (!$response->successful()) {
                $msg = $response->json('error.message') ?? ('AI request failed (' . $response->status() . ').');
                return ['success' => false, 'error' => $msg];
            }
            $text = $this->extractText($response->json());
            if ($text === '') {
                return ['success' => false, 'error' => 'AI returned an empty response. Please try again.'];
            }
            return ['success' => true, 'text' => $text];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Could not reach the AI service. Please try again.'];
        }
    }

    /** Extract a JSON object from a model response (tolerates code fences / stray prose). */
    public function parseJson(string $text)
    {
        $text = trim($text);
        $text = preg_replace('/```(json)?/i', '', $text);
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        return json_decode(substr($text, $start, $end - $start + 1), true);
    }

    /**
     * Core OpenAI call (Responses API). Returns ['success'=>bool, 'text'=>string] or ['success'=>false,'error'=>string].
     */
    public function run(string $instructions, string $input, int $maxTokens = 1500, int $timeout = 45, bool $json = false): array
    {
        $key = config('services.openai.key');
        if (empty($key)) {
            return ['success' => false, 'error' => 'AI is not configured yet.'];
        }

        try {
            $response = Http::withToken($key)
                ->timeout($timeout)
                ->acceptJson()
                ->post(config('services.openai.endpoint'), [
                    'model' => config('services.openai.model'),
                    'instructions' => $instructions,
                    'input' => $input,
                    // GPT-5 mini is a reasoning model — "minimal" skips deep reasoning so it
                    // writes the copy directly (fast + cheap); the budget covers the output text.
                    'reasoning' => ['effort' => 'minimal'],
                    'max_output_tokens' => $maxTokens,
                ] + ($json ? ['text' => ['format' => ['type' => 'json_object']]] : []));

            if (!$response->successful()) {
                $msg = $response->json('error.message') ?? ('AI request failed (' . $response->status() . ').');
                return ['success' => false, 'error' => $msg];
            }

            $text = $this->extractText($response->json());
            if ($text === '') {
                return ['success' => false, 'error' => 'AI returned an empty response. Please try again.'];
            }

            return ['success' => true, 'text' => $text];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Could not reach the AI service. Please try again.'];
        }
    }

    /** Defensive parse of the OpenAI Responses API payload. */
    private function extractText($data): string
    {
        if (!is_array($data)) {
            return '';
        }
        // Convenience field returned by some responses.
        if (!empty($data['output_text']) && is_string($data['output_text'])) {
            return trim($data['output_text']);
        }
        $parts = [];
        foreach (($data['output'] ?? []) as $item) {
            if (($item['type'] ?? '') === 'message') {
                foreach (($item['content'] ?? []) as $c) {
                    if (in_array(($c['type'] ?? ''), ['output_text', 'text'], true) && !empty($c['text'])) {
                        $parts[] = $c['text'];
                    }
                }
            }
        }
        return trim(implode("\n", $parts));
    }
}
