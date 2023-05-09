<?php

namespace Database\models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    protected $table = 'countries';
    protected $guarded = false;
}