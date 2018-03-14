<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ElementsFiles extends Model
{
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'element_id',
        'sort',
        'title',
        'path',
    ];
}