<?php

namespace App\Http\Repositories;
use App\Models\ElementsFiles;

class ElementsFilesRepository extends BaseRepository 
{
    
    public function __construct(ElementsFiles $model) 
    {
        $this->model = $model;
    }

    /* Создать */
    public function create($items) 
    {
        foreach($items as $item) {
            $this->model->updateOrCreate(['element_id' => $item['element_id'], 'path' => $item['path']], $item);
        }
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