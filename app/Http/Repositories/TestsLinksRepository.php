<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\TestsLink;

class TestsLinksRepository extends BaseRepository 
{
    
    public function __construct(TestsLink $model) 
    {
        $this->model = $model;
    }

    /* Получить все записи */
    public function all($testId) 
    {
        return $this->model->where('test_id', $testId)->with('directory');
    }

    /* Получить по ИД */
    public function find($id)
    {
        return $this->model->with('directory')->find($id);
    }

    /* Создать */
    public function create($data) 
    {
        $model = $this->model();
        $item = (new $model())->create($data);
        return $item;
    }

    /* Обновить */
    public function update($id, $data)
    {
        $item = $this->model->find($id);
        $item->fill($data);
        $item->save();
        return $item;
    }

    /* Удалить */
    public function delete($id)
    {
        $item = $this->model->find($id);
        $item->delete();
    }

    /* Удалить для теста */
    public function deleteByTestId($testId)
    {
        $this->model->where('test_id', $testId)->delete();
    }

    /* Удалить для дирекории */
    public function deleteByDirectoryId($directoryId)
    {
        $this->model->where('directory_id', $directoryId)->delete();
    }

}