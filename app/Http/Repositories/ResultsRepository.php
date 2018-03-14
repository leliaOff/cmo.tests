<?php

namespace App\Http\Repositories;
use App\Models\Results;

class ResultsRepository extends BaseRepository 
{
    
    public function __construct(Results $model) 
    {
        $this->model = $model;
    }

    /* Удалить */
    public function delete($elementId)
    {
        return $this->model->where('element_id', $elementId)->delete();
    }

}