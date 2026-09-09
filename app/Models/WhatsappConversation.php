<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappConversation extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_conversations';
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(WhatsappChatMessage::class, 'conversation_id')->orderBy('id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /** True while the AI is allowed to answer this thread. */
    public function isBotHandled(): bool
    {
        return $this->handled_by === 'bot';
    }
}
