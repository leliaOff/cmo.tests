<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ElementsData extends Model
{
    
    public $timestamps = false;
    
    protected $table = 'elements_data';
    
    protected $fillable = [
        'id',
        'element_id',
        'key',
        'value',
    ];
}