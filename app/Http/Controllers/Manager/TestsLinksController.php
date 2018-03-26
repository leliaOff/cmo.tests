<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ParseJsonService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Repositories\TestsLinksRepository;

class TestsLinksController extends Controller
{
       
    private $testsLinksRepository;

	public function __construct(TestsLinksRepository $testsLinksRepository)
	{ 
        $this->testsLinksRepository   = $testsLinksRepository;
	}

    public function index($testId)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $links = $this->testsLinksRepository->all($testId)->get()->keyBy('directory.alias');
        return $links;
    }

    /**
    * Вставить данные
    *
    */
    public function insert($testId, $directoryId)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $data = [
            'test_id'       => $testId,
            'directory_id'  => $directoryId,
        ];

        $link = $this->testsLinksRepository->create($data);
        return $link;
    }

    /**
    * Удалить данные
    *
    */
    public function delete($id)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $this->testsLinksRepository->delete($id);
    }

    /**
     * Сформировать все ссылки на тест
     */
    public function links($testId)
    {
        
        if(!Auth::check()) return ['status' => 'relogin'];
        
        $linksDirectories = $this->index($testId);
        $data = [];

        if(count($linksDirectories) == 0) {
            $data[] = [ 'title' => 'Ссылка на тест', 'link' => env('APP_URL', '') . "#/test/$testId" ];
        } else {
            $parseJsonService = new ParseJsonService();
            foreach($linksDirectories as $alias => $directory) {

                $items = DB::table($alias)->get();
                
                foreach($items as $item) {
                    $data[] =  [
                        'title'         => $item->name,
                        'data'          => $item,
                        'link'          => $parseJsonService->getLinkByDirectory($testId, $alias, $item->id) 
                    ];
                }

            }
        }

        return $data;

    }

    /**
     * Проверка верности ссылки
     */
    public function linkValidation(Request $request)
    {
        
        if(!Auth::check()) return ['status' => 'relogin'];
        
        $test   = $request['id'];
        $alias  = $request['alias'];
        $item   = $request['item'];
        $token  = $request['token'];
        
        $linksDirectories = $this->index($test);
        if(count($linksDirectories) == 0) {
            return response(['title' => ''], 200);
        }

        $parseJsonService = new ParseJsonService();
        $hash = $parseJsonService->getLinkHash($test, $alias, $item);
        if($hash == $token) {
            $directory = DB::table($alias)->where('id', $item)->first();
            return response(['title' => $directory->name], 200);
        }
        return response('fail', 403);

    }

}
