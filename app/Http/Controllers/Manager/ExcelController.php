<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Cache;

class ExcelController extends Controller
{
       
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }
    
    public function getExclude() {
        
        $logfilename = 'public/export/export.txt';
        if(!file_exists($logfilename)) return [];
        $exclude = file_get_contents($logfilename);
        $exclude = explode(",", $exclude);
        return $exclude;
    }

    public function get($id) {
        
        if(!Auth::check()) return ['status' => 'relogin'];

        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '512M');
        
        date_default_timezone_set('Etc/GMT-4');

        if($id == 1) {
            
                $filename = 'public/export/resultOfTest1.csv';
                
                $errors = [];

                //Справочник школ с МО
				$schools = Cache::remember('excel_schoolsList', 2880, function() {
					$schoolList = DB::table('schools')
						->select('municipalities.code as mo', 'schools.code as sc', 'schools.id as id')
						->join('municipalities', 'schools.municipality_id', '=', 'municipalities.id')
						->get();
					$schools = [];
					foreach($schoolList as $school) {
						$schools[$school->id] = $school;
					}
					return $schools;
				});

                //Соотношение заголовка ответа и его ИД
                $answersSetting = [
                    5 => 1, 6 => 2, 7 => 3, 8 => 4, 13 => 5, 10 => 6, 11 => 7, 12 => 8
                ];

                //Получаем элементы типа "матрица" и варианты ответов
				$elements = Cache::remember('excel_elementsList_' . $id, 2880, function() use($id) {
					
					$elementsResult = DB::table('elements')
						->where('test_id', $id)
						->orderBy('sort')
						->get();
						
						
					$elements = [];					
					//Получаем варианты ответов
					foreach($elementsResult as $i => $element) {
						$result = DB::table('elements_data')->where('element_id', $element->id)->get();
						//Разбираем данные
						$elementsData = [];
						foreach($result as $value) {
							if($value->key == 'cols' || $value->key == 'rows') {
								$items = json_decode($value->value);
								foreach($items  as $item) {
									$elementsData[$value->key][] = ['value' => $item];
								}
							} else {
								$elementsData[$value->key] = $value->value;
							}
						}
						$element->data = $elementsData;
						$elements[$element->id] = $element;
					}
					
					return $elements;
				
				});

                //Всего элементов в тесте
                $countElements = Cache::remember('excel_elementsCount_' . $id, 2880, function() use($id) {
					return DB::table('elements')->where('test_id', $id)->count();
				});
				
				//Получаем респондентов, чьи результаты у нас уже есть
				$exclude = $this->getExclude();
				
				//Только те, кто полностью прошел тест
				$respondentsList = DB::table('results')
					->select('user_key')
					->groupBy('user_key')
					->havingRaw("count(user_key) = $countElements")
					->get();
				
				$completed = [];
				foreach($respondentsList as $respondent) {
					if(in_array($respondent->user_key, $exclude) || $respondent->user_key == 0) continue;
					$completed[] = $respondent->user_key;
				}
				
				//Только для n респондентов
				$n = 1000;
				if(count($completed) > $n) {
					$completed = array_chunk($completed, $n);
					$completed = $completed [0];
				} elseif(count($completed) == 0) {
					$this->download($filename);
					exit;
				}
				
				//Log File
				$logfilename = 'public/export/export.txt';
				$log = fopen($logfilename, 'w');
				$exclude = array_merge($exclude, $completed);
				fwrite($log, implode(',', $exclude));
				fclose($log);
				//end Log File
				
				$respondents = [];
				$resultsLists = DB::table('results')
					->join('elements', 'results.element_id', '=', 'elements.id')
					->select('results.element_id as element_id', 'results.result as result', 'elements.type as type', 'results.user_key as key', 'results.id as id')
					->whereIn('results.user_key', $completed)
					->orderBy('elements.sort')
					->get();
					
				foreach($resultsLists as $result) {
					
					if(!in_array($result->key, $completed)) continue;
					
					if(!isset($respondents[$result->key])) {
						$respondents[$result->key] = [];
					}
					$respondents[$result->key][] = $result;
				}
				
                //Формируем таблицу
                $index = count($exclude) + 1;
                
                $data = [];
                if($index == 1) {
                    $data[] = ['№', 'МО', 'Код_Обр_Орг', 'Род_обучающ', 'Возраст_обучающ.воспит', //_ 20 20 19 21
                         'Q_1_1', 'Q_1_2', 'Q_1_3', 'Q_1_4', 'Q_1_5', 'Q_2_1', 'Q_2_2', 'Q_2_3', //5 6
                         'Q_3_1', 'Q_3_2', 'Q_3_3', 'Q_3_4', 'Q_3_5', 'Q_3_6',                   //7
                         'Q_4_1', 'Q_4_2', 'Q_4_3', 'Q_4_4', 'Q_4_5', 'Q_4_6', 'Q_4_7', 'Q_4_8', //8
                         'Q_5', 'Q_6_1', 'Q_6_2', 'Q_6_3', 'Q_6_4', 'Q_6_5', 'Q_6_6', 'Q_6_7',   //13 10
                         'Q_7', 'Q_8_1', 'Q_8_2', 'Q_8_3', 'Q_8_4', 'Q_8_5', 'Q_8_6', 'Q_8_7',   //11 12
                         'Q_8_8', 'Q_8_9', 'Q_8_10', 'Q_8_11', 'Q_8_12', 'Q_8_13',               //12
                         'key'
                    ];
                }
                
                foreach($respondents as $key => $results) {

                    $mo = ''; //МО
                    $sc = ''; //Код_Обр_Орг
                    $ro = ''; //Род_обучающ
                    $ye = ''; //Возраст_обучающ.воспит
                    $answers = [];
                    
                    foreach($results as $result) {

                        //Образовательные организации
                        if($result->element_id == 20) {
                            
                            if(!isset($schools[$result->result])) {
                                $sc = '';
                                $mo = '';
                            } else {
                                $sc = $schools[$result->result]->sc;
                                $mo = $schools[$result->result]->mo;
                            }
                        }

                        //Родитель/обучающийся
                        elseif($result->element_id == 19) {
                            $ro = $result->result == 0 ? 'род' : 'обучающ';
                        }

                        //Возраст
                        elseif($result->element_id == 21) {
                            $ye = $result->result + 2;
                        }

                        //Q
                        else {                           
                            
                            if($result->type == 'table') {
                                
                                $rows = json_decode($result->result);

                                foreach($rows as $i => $cols) {
                                    
                                    $value = '';
                                    foreach($cols as $j => $sell) {
                                        if($sell == true) {
                                            $value = $elements[$result->element_id]->data['cols'][$j]['value'];
                                            break;
                                        }
                                    }
                                    $answers[$result->element_id][] = $value;

                                }

                            } elseif($result->type == 'radio') {
                                $answers[$result->element_id][] = $elements[$result->element_id]->data['rows'][$result->result]['value'];
                            }

                        }

                    }

                    $value = [$index, $mo, $sc, $ro, $ye];
                    
                    $data[] = array_merge($value, $answers[5], $answers[6], $answers[7], $answers[8], $answers[13], $answers[10], $answers[11], $answers[12], [$key]);

                    $index++;

                }
                
                header('Content-Type: text/csv; charset=windows-1251');
                
                $file = fopen($filename, 'a');
                
                foreach($data as $d) {
                    $value = [];
                    foreach($d as $v) {
                        $value[] = iconv("utf-8", "windows-1251", $v);
                    }
                    fputcsv($file, $value, ";");
                }
                
                fclose($file);
                
                $this->download($filename);
                
                exit;
            
        }       

    }
    
     public function download($filename) {
            
        header("Content-type: text/csv");
        header("Content-Disposition: attachment;Filename=$filename");
        readfile($filename);
    }

}