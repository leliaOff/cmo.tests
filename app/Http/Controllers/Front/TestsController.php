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
use App\Services\ParseJsonService;

class TestsController extends Controller
{
       
    private $elementsRepository;
    private $testsRepository;
    private $parseJsonService;

	public function __construct(ElementsRepository $elementsRepository, TestsRepository $testsRepository, ParseJsonService $parseJsonService)
	{ 
		$this->elementsRepository   = $elementsRepository;
		$this->testsRepository      = $testsRepository;
		$this->parseJsonService     = $parseJsonService;
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
                'id', 'name', 'description', 'after',
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
            $data = $this->parseJsonService->parseElemensDataToArray($element->data);
            unset($element->data);
            $element->data = $data;
        }     
        
        return ['status' => 'success', 'result' => $tests, 'elements' => $elements, 'user' => $user];
    }

    /**
     * Получить и сохранить результаты
     *
     */
    public function result(Request $request)
    {
        
        $test   = $request['id'];
        $alias  = $request['alias'];
        $item   = $request['item'];
        $token  = $request['token'];

        $isValid = $this->linkValidation($test, $alias, $item, $token);
        if(!$isValid) return response('link fail', 403);        
        
        $results    =  $request['results'];
        $user       = (int)$request['user'];

        if($user < (time() - 14400)) return response('time error', 409);
        if(count($results) == 0) return response('results is null', 409);
        
        $ids = [];
        $insertList = [];
        
        foreach($results as $result) {

            $id     = (int)$result['id'];
            $type   = $result['type'];
            $result = $result['result'];

            if(empty($id)) continue;

            $result = $this->parseJsonService->parseAnswerToJson($result, $type, $id);
            if($result === false) {

                $ids[] = [
                    'id'        => $id,
                    'result'    => 'fail'
                ];
                
            } else {
                
                $insertList[] = [
                    'element_id'    => $id,
                    'result'        => $result,
                    'user_key'      => $user,
                    'alias'         => $alias,
                    'item_id'       => $item
                ];

            }

        }
        
        DB::table('results')->insert($insertList);

        return ['status' => 'success', 'result' => $ids];
        
    }

    /**
     * Проверить валидность ссылки
     */
    private function linkValidation($test, $alias, $item, $token)
    {
        
        $linksDirectories = $this->index($test);
        if(count($linksDirectories) == 0) return true;

        $parseJsonService = new ParseJsonService();
        $hash = $parseJsonService->getLinkHash($test, $alias, $item);
        if($hash == $token) return true;
        return false;

    } 

}