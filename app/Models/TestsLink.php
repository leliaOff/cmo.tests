<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TestsLink extends Model
{
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'test_id',
        'directory_id',
    ];

    /* Справочник */
    public function directory()
    {
        return $this->belongsTo('App\Models\Directory', 'directory_id');
    }

}