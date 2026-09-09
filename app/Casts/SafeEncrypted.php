<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;

/**
 * Encrypts a value at rest, but reads legacy plaintext transparently.
 *
 * Why "safe": existing rows in this project were seeded as plaintext. A plain
 * `encrypted` cast would throw a DecryptException the first time such a row is
 * read. This cast decrypts when it can and falls back to the raw value when the
 * data is still plaintext, so encryption can be rolled out without a hard cutover.
 * Every write is encrypted, so values become ciphertext as soon as they're re-saved.
 */
class SafeEncrypted implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // Legacy plaintext (not yet migrated) — return as-is.
            return $value;
        }
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return $value;
        }
        return Crypt::encryptString($value);
    }
}
