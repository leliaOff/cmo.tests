<?php

namespace App\Http\Controllers\Front;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Cache;
use App\Http\Repositories\TestsRepository;
use App\Http\Repositories\ElementsRepository;

class TestsController extends Controller
{
       
    private $elementsRepository;
    private $testsRepository;

	public function __construct(ElementsRepository $elementsRepository, TestsRepository $testsRepository)
	{ 
		$this->elementsRepository   = $elementsRepository;
		$this->testsRepository      = $testsRepository;
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
        
        $tests = $this->testsRepository->allState('published')
            ->select([
                'id', 'name', 'description',
                DB::raw('DATE_FORMAT(datetime, \'%d.%m.%Y\') as datetime'),
            ])
            ->get();

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
        $tests = $this->testsRepository->findState($id, 'published');
        
        //Список элементов
        $elements = $this->elementsRepository->all($id)
            ->select([
                'id', 'test_id', 'sort', 'title', 'is_required', 'type',
                DB::raw('REPLACE(description, "\n", "<br/>") as description'),
            ])
            ->orderBy('sort')
            ->get();

        //Данные для элементов и результаты
        foreach($elements as $i => &$element) {
            $data = $this->parseElementsData($element->data);
            unset($element->data);
            $element->data = $data;
        }     
        
        return ['status' => 'success', 'result' => $tests, 'elements' => $elements, 'user' => $user];
    }

    /**
     * Разбираем данные вопроса
     */
    private function parseElementsData($data)
    {
        //Разбираем данные
        $result = [];
        foreach($data as $value) {
            if($value->key == 'cols' || $value->key == 'rows') {
                $items = json_decode($value->value);
                foreach($items  as $item) {
                    $result[$value->key][] = ['value' => $item];
                }
            } else {
                $result[$value->key] = $value->value;
            }
        }

        return $result;
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