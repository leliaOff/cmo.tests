<?php

namespace App\Http\Repositories;

class BaseRepository {
    
    protected $model;

    protected function model() 
    {
        return get_class($this->model);
    }

}