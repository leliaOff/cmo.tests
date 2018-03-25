<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Test;

class TestsRepository extends BaseRepository 
{
    
    public function __construct(Test $model) 
    {
        $this->model = $model;
    }

    /* Получить все записи */
    public function all() 
    {
        return $this->model->where('state', '!=', 'delete')->with('elements')->with('links');
    }

    /* Получить все записи с учетом состояния */
    public function allState($state) 
    {
        return $this->model->where('state', $state)->with('elements')->with('links')
                    ->select([
                        'id', 'name', 'description', 'after',
                        DB::raw('DATE_FORMAT(datetime, \'%d.%m.%Y\') as datetime'),
                    ]);
    }

    /* Получить по ИД */
    public function find($id)
    {
        return $this->model->with('elements')->with('links')->find($id);
    }

    /* Получить по ИД */
    public function findState($id, $state)
    {
        return $this->model->where('state', $state)->with('elements')->with('links')
            ->select([
                'id', 'name', 'description', 'after',
                DB::raw('DATE_FORMAT(datetime, \'%d.%m.%Y\') as datetime'),
            ])
            ->find($id);
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

}