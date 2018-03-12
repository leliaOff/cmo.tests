<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ElementsController extends Controller
{
       
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Получить список
     *
     */
    public function select(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '512M');
        
        $elements = DB::table('elements')->where('test_id', $request['id'])->orderBy('sort')->get();
        return ['status' => 'success', 'result' => $elements];
    }

    /**
     * Получить элемент
     *
     */
    public function get(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $id = $request['id'];
        $element = DB::table('elements')->where('id', $id)->first();
        $elementsData = DB::table('elements_data')->where('element_id', $id)->get();

        //Разбираем данные
        $result = [];
        foreach($elementsData as $ed) {
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
            'sort' => $request['data']['sort'],
            'title' => $request['data']['title'],
            'is_required' => $request['data']['is_required'],
            'type' => $request['data']['type'],
        ], [
            'sort' => 'required|integer',
            'title' => 'required|string|max:255',
            'is_required' => 'boolean',
            'type' => 'required|string|in:table,checkbox,radio,title,directory',
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $id = DB::table('elements')->insertGetId(
            $request['data']
        );
        //$setting = $this->setting($request['setting'], $request['data']['type'], $id);
        return ['status' => 'success', 'result' => $id];
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

        $result = DB::table('elements')->where('id', $request['id'])->update($request['data']);
        $setting = $this->setting($request['setting'], $request['type'], $request['id']);
        return ['status' => 'success', 'result' => $result, 'setting' => $setting];
    }

    /**
    * Изменить сортировку
    *
    */
    public function sort(Request $request)
    {
        $a_sort = $request['sort'];
        $b_sort = $request['type'] == 'down' ? $a_sort + 1 : $a_sort - 1;

        $a_result = DB::table('elements')->where([
            ['test_id', $request['test_id']],
            ['sort', $b_sort]
        ])->update(['sort' => $a_sort]);

        $b_result = DB::table('elements')->where('id', $request['id'])->update(['sort' => $b_sort]);

        return ['status' => 'success', 'a_result' => $a_result, 'b_result' => $b_result];
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
                    'count' => 'required|integer|min:1|max:' . count($cols),
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
                    'count' => 'required|integer|min:1|max:' . count($rows),
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

        DB::table('elements_data')->where('element_id', $id)->delete();
        DB::table('elements_data')->insert($setting);
        return ['status' => 'success', 'result'  => $setting];

    }

    /**
    * Удалить данные
    *
    */
    public function delete(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        
        DB::table('elements_data')->where('element_id', $request['id'])->delete();
        DB::table('elements')->where('id', $request['id'])->delete();
        
        $element = DB::table('elements')->where('id', $request['id'])->first();
        $elementsData = DB::table('elements_data')->where('element_id', $request['id'])->get();

        $this->resort($request['test_id']);

        return ['status' => 'success', 'result' => $element, 'setting' => $elementsData];
    }

    /**
    * Пересорт
    *
    */
    public function resort($test_id)
    {
        $results = DB::table('elements')->where('test_id', $test_id)->orderBy('sort')->get();
        foreach($results as $i => $value) {
            DB::table('elements')->where('id', $value->id)->update(['sort' => $i]);
        }
    }

}
