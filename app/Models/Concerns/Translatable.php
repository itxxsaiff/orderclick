<?php

namespace App\Models\Concerns;

/**
 * Store content in the visitor's language.
 *
 * The model lists its translatable columns in TRANSLATABLE. The original text stays in those
 * columns (whatever language the owner typed); `translations` holds the other languages plus a
 * hash of the original ("_src"). When the owner edits the original, the hash no longer matches
 * and the stale translations are ignored until the store is translated again.
 */
trait Translatable
{
    public function initializeTranslatable(): void
    {
        $this->mergeCasts(['translations' => 'array']);
    }

    /**
     * New or edited text is translated automatically right after the page has been sent to the
     * owner (so saving never gets slower). Console commands (imports, seeders) are skipped;
     * `php artisan store:translate` covers those.
     */
    public static function bootTranslatable(): void
    {
        static::saved(function ($model) {
            if (app()->runningInConsole() || empty($model->vendor_id)) {
                return;
            }
            if ($model->wasRecentlyCreated || $model->wasChanged(static::TRANSLATABLE)) {
                \App\Services\StoreTranslator::afterResponse((int) $model->vendor_id);
            }
        });
    }

    /** Hash of the original texts the current translations were made from. */
    public function sourceHash(): string
    {
        $src = [];
        foreach (static::TRANSLATABLE as $f) {
            $src[$f] = trim((string) $this->getAttribute($f));
        }

        return md5(json_encode($src, JSON_UNESCAPED_UNICODE));
    }

    /** Fields that actually have text (empty ones need no translation). */
    public function translatableSource(): array
    {
        $out = [];
        foreach (static::TRANSLATABLE as $f) {
            $v = trim((string) $this->getAttribute($f));
            if ($v !== '') {
                $out[$f] = $v;
            }
        }

        return $out;
    }

    public function translationIsCurrent(): bool
    {
        $t = $this->getAttribute('translations');

        return is_array($t) && ($t['_src'] ?? null) === $this->sourceHash();
    }

    /** The field in the given (or current) language, else the original text. */
    public function tr(string $field, ?string $lang = null): string
    {
        $orig = (string) $this->getAttribute($field);
        $lang = $lang ?: app()->getLocale();
        if (!$this->translationIsCurrent()) {
            return $orig;
        }
        $v = $this->getAttribute('translations')[$lang][$field] ?? null;

        return is_string($v) && trim($v) !== '' ? $v : $orig;
    }
}
