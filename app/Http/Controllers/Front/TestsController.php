<?php

namespace App\Http\Controllers\Front;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Cache;

class TestsController extends Controller
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
     * Show the application dashboard.
     *
     * @return void
     */
    public function index()
    {
        
    }

    /**
     * Получить список
     *
     */
    public function select()
    {
        
        $tests = Cache::remember('front_tests', 2880, function() {
            return DB::table('tests')->where('state', 'published')->select([
                'id', 'name', 'description',
                DB::raw('DATE_FORMAT(datetime, \'%d.%m.%Y\') as datetime'),
            ])->get();
        });
        return ['status' => 'success', 'result' => $tests];
    }

    /**
     * Получить элемент
     *
     */
    public function get(Request $request)
    {
        $id = $request['id'];
        $user = $request['user'];
        if($user == 0) $user = time();

        //Информация по самому тесту
        $tests = Cache::remember('front_test_' . $id, 2880, function() use($id) {
            return DB::table('tests')
                ->where([
                    ['id', $id],
                    ['state', 'published']
                ])
                ->select([
                    'id', 'name', 'description', 'after',
                    DB::raw('DATE_FORMAT(datetime, \'%d.%m.%Y\') as datetime'),
                ])
                ->first();
        });
        
        //Список элементов
        $elements = Cache::remember('front_elements_' . $id, 2880, function() use($id) {
            return DB::table('elements')
                ->where('test_id', $id)
                ->select([
                    'id', 'test_id', 'sort', 'title', 'is_required', 'type',
                    DB::raw('REPLACE(description, "\n", "<br/>") as description'),
                ])
                ->orderBy('sort')->get();
        });

        //Данные для элементов и результаты

        foreach($elements as $i => $element) {

            $element_id = $element->id;
            $elements[$i]->data = Cache::remember('elements_data_' . $element_id, 2880, function() use($element_id) {
                
                $result = DB::table('elements_data')->where('element_id', $element_id)->get();

                //Разбираем данные
                $data = [];
                foreach($result as $value) {
                    if($value->key == 'cols' || $value->key == 'rows') {
                        $items = json_decode($value->value);
                        foreach($items  as $item) {
                            $data[$value->key][] = ['value' => $item];
                        }
                    } else {
                        $data[$value->key] = $value->value;
                    }
                }

                return $data;

            });

            // //Результаты
            // $result = DB::table('results')->where([
                // ['element_id', $element->id],
                // ['user_key', $user],
            // ])->first();

            // //Разбираем данные
            // if(!empty($result)) {
                // $result = $result->result;
                // if($element->type == 'table' || $element->type == 'checkbox') {
                    // $result = json_decode($result);
                // }
                // $elements[$i]->result = $result;
            // }

        }     
        
        return ['status' => 'success', 'result' => $tests, 'elements' => $elements, 'user' => $user];
    }

    /**
     * Получить и сохранить результаты
     *
     */
    public function result(Request $request)
    {
        
        $results =  $request['results'];
        $user = (int)$request['user'];

        if($user < (time() - 14400)) return ['status' => 'fail', 'error' => 'time error'];
        if(count($results) == 0) return ['status' => 'fail', 'error' => 'results is null'];
        
        // //Удаляем предыдущие ответы этого пользователя
        // DB::table('results')->where([
            // 'user_key' => $user
        // ])->delete();
        
        $ids = [];
        $insertList = [];
        
        foreach($results as $result) {

            $id = (int)$result['id'];
            $type = $result['type'];
            $result = $result['result'];

            $result = $this->resultsConvert($result, $type, $id);
            if($result === false) {

                $ids[] = [
                    'id' => $id,
                    'result' => 'fail'
                ];
                
            } else {
                
                $insertList[] = [
                    'element_id' => $id,
                    'result' => $result,
                    'user_key' => $user
                ];

            }

        }
        
        DB::table('results')->insert($insertList);

        return ['status' => 'success', 'result' => $ids];
        
    }

    /**
     * Обработка результатов в зависимости от типа
     *
     */
    public function resultsConvert($result, $type, $id)
    {

        if($type == 'table') {
            
            foreach($result as $i => $cols) {
                foreach($cols as $j => $cell) {
                    $result[$i][$j] = (bool)$cell;
                }
            }

            $result = json_encode($result);

        } elseif($type == 'checkbox') {
            
            foreach($result as $i => $value) {
                $result[$i] = (bool)$value;
            }

            $result = json_encode($result);

        } elseif($type == 'radio') {

            $result = (int)$result;
            if($result < 0) $result = false;

        } elseif($type == 'directory') {

            $result = (int)$result;
            
            //Что за справочник
            $dataResult = DB::table('elements_data')->
                where([
                    ['element_id', $id],
                    ['key', 'alias'],
                ])->
                first();
            if(empty($dataResult)) return ['status' => 'fail'];
            $alias = $dataResult->value;

            //Есть ли в нем такой элемент
            $count = DB::table($alias)->where('id', $result)->count();
            if($count == 0) $result = false;


        } else {
            $result = false;
        }

        return $result;
    }

}