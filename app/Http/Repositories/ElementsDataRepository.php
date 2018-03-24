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
            if(!empty($data['id_delete'])) {
                $this->deleteByKey($data['element_id'], $data['key']);
            } else {
                $this->model->updateOrCreate(['element_id' => $data['element_id'], 'key' => $data['key']], $data);
            }
        }
    }

    /* Удалить по ключу */
    private function deleteByKey($elementId, $key)
    {
        return $this->model->where('element_id', $elementId)->where('key', $key)->delete();
    }

    /* Удалить */
    public function delete($elementId)
    {
        return $this->model->where('element_id', $elementId)->delete();
    }

}