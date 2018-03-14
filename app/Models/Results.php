<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Results extends Model
{
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'element_id',
        'result',
        'user_key',
        'datetime',
    ];

}