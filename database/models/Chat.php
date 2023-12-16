<?php

namespace Database\models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    protected $guarded = false;

    public function increaseTheNumberOfAttempts(): void
    {
        $this->increment(column: 'attempts');
    }
}
