<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'quotes';

    protected $fillable = [
        'code',
        'code_in',
        'description',
        'bid',
        'ask',
        'timestamp',
    ];

    public $timestamps = false;
}
