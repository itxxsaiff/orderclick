<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

/**
 * One-time, idempotent encryption of secrets that were seeded as plaintext:
 *   - payments.secret_key, payments.encryption_key
 *   - settings.mail_password
 *
 * Uses the query builder (not Eloquent) so it reads/writes raw column values and
 * is unaffected by the SafeEncrypted cast. Re-running is a no-op: already-encrypted
 * values decrypt cleanly and are skipped.
 */
return new class extends Migration
{
    private array $targets = [
        'payments' => ['secret_key', 'encryption_key'],
        'settings' => ['mail_password'],
    ];

    public function up(): void
    {
        foreach ($this->targets as $table => $columns) {
            foreach (DB::table($table)->get(array_merge(['id'], $columns)) as $row) {
                $update = [];
                foreach ($columns as $col) {
                    $val = $row->$col;
                    if ($val === null || $val === '' || $this->isEncrypted($val)) {
                        continue;
                    }
                    $update[$col] = Crypt::encryptString($val);
                }
                if (!empty($update)) {
                    DB::table($table)->where('id', $row->id)->update($update);
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->targets as $table => $columns) {
            foreach (DB::table($table)->get(array_merge(['id'], $columns)) as $row) {
                $update = [];
                foreach ($columns as $col) {
                    $val = $row->$col;
                    if ($val === null || $val === '') {
                        continue;
                    }
                    try {
                        $update[$col] = Crypt::decryptString($val);
                    } catch (\Throwable $e) {
                        // already plaintext — leave it
                    }
                }
                if (!empty($update)) {
                    DB::table($table)->where('id', $row->id)->update($update);
                }
            }
        }
    }

    private function isEncrypted($value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
