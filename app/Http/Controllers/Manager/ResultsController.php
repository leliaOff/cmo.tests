<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Manager\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Repositories\TestsLinksRepository;

use Illuminate\Support\Facades\Cache;

class ResultsController extends Controller
{
       
    private $testsLinksRepository;

	public function __construct(TestsLinksRepository $testsLinksRepository)
	{ 
        $this->testsLinksRepository   = $testsLinksRepository;
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
    public function select(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        //Статистика в разрезе
        $incisions = isset($request['incisions']) ? $request['incisions'] : false;
        if($incisions) $incisions = $this->getIncision($incisions);

        //Общая статистика
        $general = $this->getGeneralResults($request['id'], $incisions);
        
        //Список элементов
        $test_id = $request['id'];
        $elements = Cache::remember('manager_elements_' . $test_id, 60, function() use($test_id) {
            return DB::table('elements')->where('test_id', $test_id)->orderBy('sort')->get();
        });

        //Данные для элементов и результаты
        foreach($elements as $i => $element) {

            //Не нужны элементы, которые не для заполнения
            if($element->type == 'title') {
                continue;
            }

            //Статистика по элементу
            $elementStat = $this->getElementStat($element, $general['countPeople'], $incisions);
            $elements[$i]->stat = $elementStat;

            //Варианты ответов
            $element_id = $element->id;
            $result = Cache::remember('manager_elements_data_' . $element_id, 60, function() use($element_id) {
                return DB::table('elements_data')->where('element_id', $element_id)->get();
            });
            
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
            $elements[$i]->data = $data;            

        }

        return ['status' => 'success', 'result' => $elements, 'general' => $general, 'incisions' => $incisions];
    }

    /**
     * Получить список пользователей для разреза
     *
     */
    public function getIncision($incisions)
    {        
        $users = Cache::remember('manager_getIncision_users', 60, function() {
            $result = DB::table('results')->select('user_key')->groupBy('user_key')->get();
            $users = [];
            foreach($result as $value) {
                $users[] = $value->user_key;
            }
            return $users;
        });
        
        //Объединяем с другими результатми, если разрезов несколько
        foreach($incisions as $i => $incision) {

            if(empty($incision)) continue;
            
            $incisionUsers = Cache::remember('manager_incisionUsers_' . $i . '_' . $incision, 60, function() use($i, $incision) {
                
                $result = DB::table('results')->select('user_key')->where([
                    'element_id' => $i,
                    'result' => $incision
                ])->groupBy('user_key')->get();

                $incisionUsers = [];
                foreach($result as $value) {
                    $incisionUsers[] = $value->user_key;
                }

                return $incisionUsers;

            });

            $users = array_intersect($users, $incisionUsers);
        }

        //Собираем "where" массив
        $where = [];
        foreach($users as $user) {
            $where[] = ['user_key' => $user];
        }

        return $where;
    }

    /**
     * Получить общую статистику: количество и процент
     * TODO: переделать запросы, что бы получать результат в рамках одного теста
     */
    public function getGeneralResults($test_id, $incisions)
    {      
        
        //Количество респондентов  
		$countPeople = Cache::remember('manager_getGeneralResults_countPeople', 10, function() use($incisions) {
            $query = DB::table('results')->select('user_key');
            if($incisions) {
                foreach($incisions as $incision) { //разрез
                    $query->orWhere($incision);
                }
            }
            $result = $query->groupBy('user_key')->get();
            return count($result);
        });

        //Всего элементов в тесте        
        $countElements = Cache::remember('manager_getGeneralResults_countElements_' . $test_id, 1440, function() use($test_id) {
            return DB::table('elements')->where('test_id', $test_id)->count();
        });

        //Время обновления
        date_default_timezone_set('Etc/GMT-4');
        $tm = Cache::remember('manager_getGeneralResults_tm_' . $test_id, 10, function() {
            return date('H:i:s');
        });
        
        return [
            'countPeople'   => $countPeople,
            'countElements' => $countElements,
            'tm'            => $tm
        ];
    }

    /**
     * Получить статистику для каждого элемента: количество
     *
     */
    public function getElementStat($element, $countPeople, $incisions)
    {
        //Количество ответов
        $query = DB::table('results')->where('element_id', $element->id);
        if($incisions) {
            $query->where(function ($query) use($incisions) {
                foreach($incisions as $incision) { //разрез
                    $query->orWhere($incision);
                }
            });
        }            
        
        $countResult = $query->count();

        return ['count' => $countResult];
    }

    /**
     * Получить статистику для каждого варианта ответа: количество и процент
     *
     */

    public function getResultStat(Request $request)
    {
        
        if(!Auth::check()) return ['status' => 'relogin'];

        $element    = $request['item'];
        $data       = $element['data'];
        $incisions  = isset($request['incisions']) ? $request['incisions'] : false;
        if($incisions) $incisions = $this->getIncision($incisions);
        
        if($element['type'] == 'table') {

            //Матрица вариантов ответов
            $matrix = [];
            //Всего дано ответов на данный вопрос
            $allCount = [];

            foreach($data['rows'] as $i => $row) {
                
                $matrix[$i] = [];
                $allCount[$i] = 0;

                foreach($data['cols'] as $j => $col) {
                    $matrix[$i][$j] = ['count' => 0];
                }
            }

        } elseif($element['type'] == 'checkbox' || $element['type'] == 'radio') {

            //Матрица вариантов ответов
            $matrix = [];
            //Всего дано ответов на данный вопрос
            $allCount = 0;

            foreach($data['rows'] as $i => $row) {
                $matrix[$i] = ['count' => 0];
            }

        } elseif($element['type'] == 'directory') {
            
            //Матрица вариантов ответов
            $matrix = [];
            //Получаем справочник
            $directories = DB::table($data['alias'])->select('id', 'name')->get();
            foreach($directories as $directory) {
                $matrix[$directory->id] = [
                    'id' => $directory->id,
                    'name' => $directory->name,
                    'count' => 0
                ];
            }

        }

        //Результаты
        $query = DB::table('results')->where('element_id', $element['id']);
        if($incisions) {
            $query->where(function ($query) use($incisions) {
                foreach($incisions as $incision) { //разрез
                    $query->orWhere($incision);
                }
            });
        }
            
        $results = $query->get();
        if(empty($results)) return ['count' => '0'];
        
        //Разбираем данные
        foreach($results as $result) {                
            
            $result = $result->result;
            if($element['type'] == 'table') {
                
                $result = json_decode($result);

                //Бегаем по матрице, считаем true
                foreach($result as $i => $cols) {
                    
                    foreach($cols as $j => $cell) {
                        
                        if($cell == true) {

                            $matrix[$i][$j]['count']++;

                            //Общее количество ответов
                            $allCount[$i]++;

                        }
                    }
                }

            } elseif($element['type'] == 'checkbox') {
                
                $result = json_decode($result);

                //Бегаем по матрице, считаем true
                foreach($result as $i => $cell) {
                    if($cell === true) {

                        $matrix[$i]['count']++;

                        //Общее количество ответов
                        $allCount++;

                    } elseif(is_string($cell) && $cell != '') {
                        
                        $matrix[$i]['count']++;
                        //Общее количество ответов
                        $allCount++;
                    }
                }

            } elseif($element['type'] == 'radio') {
                
                if(!isset($matrix[$result])) {
                    $result = count($matrix) - 1;
                }

                $matrix[$result]['count']++;
                $matrix[$result]['percent'] = round(($matrix[$result]['count'] / $element['stat']['count']) * 100, 2);
                
            } elseif($element['type'] == 'directory') {
                
                if(!isset($matrix[$result])) continue;
                $matrix[$result]['count']++;
                $matrix[$result]['percent'] = round(($matrix[$result]['count'] / $element['stat']['count']) * 100, 2);
                
            }

        }

        //Пересчитываем процент
        if($element['type'] == 'table') {
            
            foreach($matrix as $i => $cols) {
                foreach($cols as $j => $value) {                    
                    $matrix[$i][$j]['percent'] = round(($value['count'] / $allCount[$i]) * 100, 2);
                }
            }

        } elseif($element['type'] == 'checkbox') {
            
            foreach($matrix as $i => $value) {
                $matrix[$i]['percent'] = round(($value['count'] / $allCount) * 100, 2);
                $matrix[$i]['percent'] = round(($value['count'] / $allCount) * 100, 2);
            }

        }

        return $matrix;

    }
    
    function cleanResults() {
        
        $results = DB::table('results')
            ->select('results.user_key as key')
            ->groupBy('user_key')
            ->havingRaw("count(user_key) < 11")
            ->limit(10000)
            ->get();
            
        foreach($results as $value) {
            echo $value->key . '<br>';
            DB::table('results')->where([
                ['user_key', '=', $value->key],
                ['datetime', '<', '2017-09-14 14:00:00']
            ])->delete();
        }
    }

    /**
     * Статистика по ссылкам
     */
    public function getLinksResult($id)
    {
        
        if(!Auth::check()) return ['status' => 'relogin'];
        
        /* Полчаем все ссылки на тест */
        $linksDirectories = $this->testsLinksRepository->all($id)->get()->keyBy('directory.alias');
        if(count($linksDirectories) == 0) {
            return [];
        }

        $data = [];
        foreach($linksDirectories as $alias => $directory) {

            $items = DB::table($alias)->get();
            
            foreach($items as $item) {
                $data[] =  [
                    'title'         => $item->name,
                    'id'            => $item->id,
                    'alias'         => $alias,
                    'data'          => $item,
                ];
            }

        }

        /* Список ИД элементов теста */
        $elementsIds = Cache::remember('manager_elements_ids_' . $id, 60, function() use($id) {
            $elements = DB::table('elements')->where('test_id', $id)->orderBy('sort')->get();
            $result = [];
            foreach($elements as $element) {
                $result[] = $element->id;
            }
            return $result;
        });

        /* Собираем статистику по каждой */
        foreach($data as &$value) {
            $query = DB::table('results')
                ->select('user_key')
                ->distinct()
                ->whereIn('element_id', $elementsIds)
                ->where('alias', $value['alias'])
                ->where('item_id', $value['id'])
                ->get();
            $value['count'] = count($query);
        }

        return $data;
    }

}
