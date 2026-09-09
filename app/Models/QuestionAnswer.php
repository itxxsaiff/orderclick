<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;
    protected $table = 'question_answer';
    protected $guarded = [];

    /** Merchant-authored FAQ the AI may use on the website and on WhatsApp. */
    public function scopeKnowledgeBase($query)
    {
        return $query->where('source', 'knowledge_base');
    }
    public function product()
    {
        return $this->hasOne('App\Models\Item', 'id', 'product_id');
    }
}
