<?php

namespace App\Http\Repositories;
use App\Models\Elements;
use App\Http\Repositories\ElementsDataRepository;
use App\Http\Repositories\ElementsFilesRepository;
use App\Http\Repositories\ResultsRepository;

class ElementsRepository extends BaseRepository 
{
    
    private $data;
    private $files;
    private $results;
    
    public function __construct(Elements $model, ElementsDataRepository $data, ElementsFilesRepository $files, ResultsRepository $results) 
    {
        $this->model = $model;
        $this->data = $data;
        $this->files = $files;
        $this->results = $results;
    }

    /* Получить все записи */
    public function all($testId) 
    {
        return $this->model->where('test_id', $testId)->with('files')->with('data');
    }

    /* Получить по ИД */
    public function find($id)
    {
        return $this->model->with('files')->with('data')->find($id);
    }

    /* Создать */
    public function create($data) 
    {
        $model = $this->model();
        $item = (new $model())->create($data);

        return $item;
    }

    /* Обновить */
    public function update($id, $data, $setting, $files)
    {
        $item = $this->model->find($id);
        $item->fill($data);
        
        $item->save();

        //setting
        $this->data->create($setting);

        //files
        $filesData = [];

        foreach($files as $i => $file) {
            if(isset($file['is_delete']) && $file['is_delete'] === true) {
                if(isset($file['id'])) {
                    $this->files->delete($file['id']);
                }                
            } else {
                $filesData[] = [
                    'element_id'    => $item->id,
                    'sort'          => $i,
                    'title'         => $item->title,
                    'path'          => $file['path']
                ];
            }
        }
        $this->files->create($filesData);

        return $this->model->with('files')->with('data')->find($id);
    }

    /* Удалить */
    public function delete($id)
    {
        $this->data->delete($id);
        $this->results->delete($id);

        $item = $this->model->find($id);
        $testId = $item->test_id;
        $item->delete();

        $this->resort($testId);
    }

    /* Поменять местами - сортировка */
    public function sort($id, $sort)
    {
        $item = $this->model->find($id);
        $second = $item->sort;
        $item->sort = ($sort == 'down' ? ($second + 1) : ($second - 1));

        //count
        $count = $this->model->where('test_id', $item->test_id)->count();
        if($item->sort < 0 || $item->sort >= $count) return false;

        $this->model->where('test_id', $item->test_id)->where('sort', $item->sort)->update(['sort' => $second]);
        $item->save();

        return true;
    }

    /* Пересортировать */
    public function resort($testId)
    {
        $items = $this->all($testId)->orderBy('sort')->get();
        foreach($items as $i => $item) {
            $item->sort = $i;
            $item->save();
        }
    }

}