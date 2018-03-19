<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'name',
        'description',
        'datetime',
        'type',
        'template',
        'uniqueness_type',
        'publication',
        'timeout',
        'state',
        'after',
    ];

    /* Данные элемента */
    public function elements()
    {
        return $this->hasMany('App\Models\Elements', 'test_id');
    }

}