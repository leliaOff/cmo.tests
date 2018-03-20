<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ElementsCondition extends Model
{
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'element_id',
        'conditions_element_id',
        'conditions_answer',
        'operand',
        'combination',
    ];
}