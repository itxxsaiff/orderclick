<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\SafeEncrypted;

class Settings extends Model
{
    use HasFactory;
    protected $table = 'settings';

    // Encrypt the outgoing-mail (SMTP) password at rest; read transparently.
    protected $casts = [
        'mail_password' => SafeEncrypted::class,
    ];
}
