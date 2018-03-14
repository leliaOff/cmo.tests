<?php

namespace App\Http\Repositories;
use App\Models\ElementsData;

class ElementsDataRepository extends BaseRepository 
{
    
    public function __construct(ElementsData $model) 
    {
        $this->model = $model;
    }

    /* Создать */
    public function create($settings) 
    {
        foreach($settings as $data) {
            $this->model->updateOrCreate(['element_id' => $data['element_id'], 'key' => $data['key']], $data);
        }
    }

    /* Удалить */
    public function delete($elementId)
    {
        return $this->model->where('element_id', $elementId)->delete();
    }

}