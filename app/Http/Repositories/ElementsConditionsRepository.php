<?php

namespace App\Http\Repositories;
use App\Models\ElementsCondition;

class ElementsConditionsRepository extends BaseRepository 
{
    
    public function __construct(ElementsCondition $model) 
    {
        $this->model = $model;
    }

    /* Создать */
    public function create($data) 
    {
        $model = $this->model();
        $item = (new $model())->create($data);
        return $item;
    }

    /* Изменить */
    public function update($id, $data) 
    {
        $item = $this->model->find($id);
        $item->fill($data);
        $item->save();
    }

    /* Удалить */
    public function delete($id)
    {
        $item = $this->model->find($id);
        $item->delete();
    }

    /* Удалить все */
    public function deleteAll($elementId)
    {
        $this->model->where('element_id', $elementId)->delete();
    }

}