<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text',
        'type_question',
        'type_input_open'
    ];


    public function response(): HasMany
    {
        return $this->hasMany(Response::class);
    }
    public function default_answer(): HasMany
    {
        return $this->hasMany(Default_answer::class);
    }
}
