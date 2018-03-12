<?php

namespace App\Http\Controllers\Front;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Cache;

class DirectoriesController extends Controller
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
    public function select(Request $request)
    {
        $alias = $request['alias'];
        $result = Cache::remember('directory_' . $alias, 60, function() use($alias) {
            return DB::table($alias)->select('id', 'name')->get();
        });
        return ['status' => 'success', 'result' => $result];
    }

}
