<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\SafeEncrypted;

class Settings extends Model
{
    use HasFactory, \App\Models\Concerns\Translatable;
    protected $table = 'settings';

    /** The store's own description, shown on the home / about pages and in the footer. */
    public const TRANSLATABLE = ['description'];

    // Encrypt the outgoing-mail (SMTP) password at rest; read transparently.
    protected $casts = [
        'mail_password' => SafeEncrypted::class,
    ];
}
