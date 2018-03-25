<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $table = 'directories';

    public $timestamps = false;
    
    protected $fillable = [
        'alias',
        'title',
        'description',
    ];

}