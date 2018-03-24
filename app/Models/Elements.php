<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Elements extends Model
{
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'test_id',
        'sort',
        'title',
        'description',
        'is_required',
        'type',
    ];

    /* Данные элемента */
    public function data()
    {
        return $this->hasMany('App\Models\ElementsData', 'element_id');
    }

    /* Файлы элемента */
    public function files()
    {
        return $this->hasMany('App\Models\ElementsFiles', 'element_id');
    }

    /* Условия */
    public function conditions()
    {
        return $this->hasMany('App\Models\ElementsCondition', 'element_id');
    }

}