<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\ParseJsonService;
use App\Http\Repositories\ElementsRepository;

use Illuminate\Support\Facades\Storage;
use Image;

class ElementsController extends Controller
{
       
    private $elementsRepository;
    private $parseJsonService;

	public function __construct(ElementsRepository $elementsRepository, ParseJsonService $parseJsonService)
	{ 
        $this->elementsRepository   = $elementsRepository;
        $this->parseJsonService     = $parseJsonService;
	}

    /**
     * Получить список
     *
     */
    public function select(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        
        $elements = $this->elementsRepository->all($request['id'])
            ->orderBy('sort')
            ->get();
            
        return ['status' => 'success', 'result' => $elements];
    }

    /**
     * Получить элемент
     *
     */
    public function get(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $element = $this->elementsRepository->find($request['id']);

        //Разбираем данные
        $result = [];
        foreach($element->data as $ed) {
            if($ed->key == 'cols' || $ed->key == 'rows') {
                $items = json_decode($ed->value);
                foreach($items  as $item) {
                    $result[$ed->key][] = ['value' => $item];
                }
            } else {
                $result[$ed->key] = $ed->value;
            }
        }

        return ['status' => 'success', 'result' => $element, 'setting' => $result];
    }

    /**
    * Вставить данные
    *
    */
    public function insert(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $validator = Validator::make([
            'sort'          => $request['data']['sort'],
            'title'         => $request['data']['title'],
            'is_required'   => $request['data']['is_required'],
            'type'          => $request['data']['type'],
        ], [
            'sort'          => 'required|integer',
            'title'         => 'required|string|max:255',
            'is_required'   => 'boolean',
            'type'          => 'required|string|in:table,checkbox,radio,title,directory',
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $element = $this->elementsRepository->create($request['data']);

        return ['status' => 'success', 'result' => $element->id];
    }

    /**
    * Изменить данные
    *
    */
    public function update(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $validator = Validator::make([
            'title' => $request['data']['title'],
            'is_required' => $request['data']['is_required'],
        ], [
            'title' => 'required|string|max:255',
            'is_required' => 'boolean',
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        //Настройки элемента
        $setting = $this->setting($request['setting'], $request['type'], $request['id']);
        
        //Условия отображения элемента
        $conditions = $this->conditions($request['conditions'], $request['id']);

        $element = $this->elementsRepository->update($request['id'], $request['data'], $setting, $request['files'], $conditions);

        //Удаляем файлы 
        $deleteFilesList = [];
        foreach($request['files'] as $file) {
            if(isset($file['is_delete']) && $file['is_delete'] === true) {
                $deleteFilesList[] = $file['path'];
            }
        }
        $this->deleteFile($deleteFilesList);
        
        return [ 'status' => 'success', 'result' => $element, 'setting' => $setting, 'conditions' => $conditions ];
    }

    /**
    * Изменить сортировку
    *
    */
    public function sort(Request $request)
    {
        return ['status' => 'success', 'sort' => $request['sort'], 'result' => $this->elementsRepository->sort($request['id'], $request['sort'])];
    }

    /**
    * Вставить настройки
    *
    */
    public function setting($data, $type, $id)
    {      
        $setting = [];

        switch($type) {
            /* Таблица */
            case 'table':

                $cols = [];
                foreach($data['cols'] as $value) $cols[] = $value['value'];
                $rows = [];
                foreach($data['rows'] as $value) $rows[] = $value['value'];
                
                $validator = Validator::make([
                    'cols' => $cols,
                    'rows' => $rows,
                    'count' => $data['count'],
                ], [
                    'cols' => 'array',
                    'rows' => 'array',
                    'count' => 'required|integer|min:1',
                ]);

                if($validator->fails()) {                    
                    return ['status' => 'fail', 'error'  => $validator->messages()];        
                }

                $setting = [
                    ['element_id' => $id, 'key' => 'cols', 'value' => json_encode($cols)],
                    ['element_id' => $id, 'key' => 'rows', 'value' => json_encode($rows)],
                    ['element_id' => $id, 'key' => 'count', 'value' => $data['count']],
                ];

                break;

            /* Многие из многих */
            case 'checkbox':

                $rows = [];
                foreach($data['rows'] as $value) $rows[] = $value['value'];

                $validator = Validator::make([
                    'rows' => $rows,
                    'count' => $data['count'],
                ], [
                    'rows' => 'required|array|min:1',
                    'count' => 'required|integer|min:1',
                ]);

                if($validator->fails()) {                    
                    return ['status' => 'fail', 'error'  => $validator->messages()];        
                }

                $setting = [
                    ['element_id' => $id, 'key' => 'rows', 'value' => json_encode($rows)],
                    ['element_id' => $id, 'key' => 'count', 'value' => $data['count']],
                ];

                break;

            /* Один из многих */
            case 'radio':

                $rows = [];
                foreach($data['rows'] as $value) $rows[] = $value['value'];

                $validator = Validator::make([
                    'rows' => $rows,
                ], [
                    'rows' => 'required|array|min:1',
                ]);

                if($validator->fails()) {                    
                    return ['status' => 'fail', 'error'  => $validator->messages()];        
                }

                $setting = [
                    ['element_id' => $id, 'key' => 'rows', 'value' => json_encode($rows)],
                ];

                break;

            /* Заголовок и описание */
            case 'title':

                return ['status' => 'success', 'result'  => []];
                // $validator = Validator::make([
                //     'data' => $data['data'],
                // ], [
                //     'data' => 'required|string',
                // ]);

                // if($validator->fails()) {                    
                //     return ['status' => 'fail', 'error'  => $validator->messages()];        
                // }

                // $setting = [
                //     ['element_id' => $id, 'key' => 'data', 'value' => $data['data']],
                // ];

                break;

            /* Элемент справочника */
            case 'directory':

                $validator = Validator::make([
                    'alias' => $data['alias'],
                ], [
                    'alias' => 'required|string',
                ]);
                
                if($validator->fails()) {                    
                    return ['status' => 'fail', 'error'  => $validator->messages()];        
                }

                $setting = [
                    ['element_id' => $id, 'key' => 'alias', 'value' => $data['alias']],
                ];

                break;
        }

        // DB::table('elements_data')->where('element_id', $id)->delete();
        // DB::table('elements_data')->insert($setting);
        return $setting;

    }

    /**
     * Сформировать условия
     */
    private function conditions($conditions, $elementId)
    {
        foreach($conditions as &$condition) {
            $condition['conditions_answer'] = $this->parseJsonService->parseAnswerToJson($condition['conditions_answer'], $condition['element']['type'], $condition['conditions_element_id']);
            $condition['element_id'] = $elementId;
            unset($condition['element']);
        }

        return $conditions;
    }

    /**
    * Удалить данные
    *
    */
    public function delete(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $this->elementsRepository->delete($request['id']);
        return ['status' => 'success'];
    }

    /**
	 * Загрузить картинку
	 * 
	 * @return json
	 */
    public function uploadFile(Request $request)
    {
		$result = [];
        foreach($request['files'] as $file) {
            $file 		= $file->store('public/images');
            $filename 	= 'public' . Storage::url($file);
			$img 		= Image::make($filename);
			$img->fit(900);
            $img->save($filename);
            $result[] = $filename;
        }

        return $result;
    }
    
    /**
     * Удалить картинки
     */
    private function deleteFile($files)
    {
        foreach($files as $file) {
            Storage::delete($file);
        }
    }

}
