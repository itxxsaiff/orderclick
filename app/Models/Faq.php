<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory, \App\Models\Concerns\Translatable;
    protected $table = 'faqs';

    public const TRANSLATABLE = ['question', 'answer'];
}