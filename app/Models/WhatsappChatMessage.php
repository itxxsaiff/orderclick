<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappChatMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_chat_messages';
    protected $guarded = [];

    public function isIncoming(): bool
    {
        return $this->direction === 'in';
    }
}
