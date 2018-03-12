<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    public function select()
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $directories = DB::table('directories')->get();
        return ['status' => 'success', 'result' => $directories];
    }

    /**
     * Получить элемент
     *
     */
    public function get(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $id = $request['id'];
        $directory = DB::table('directories')->where('id', $id)->first();
        return ['status' => 'success', 'result' => $directory];
    }

    /**
     * Вставить данные
     *
     */
    public function insert(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $validator = Validator::make([
            'alias' => $request['data']['alias'],
            'title' => $request['data']['title']
        ], [
            'alias' => 'required|string|max:255',
            'title' => 'required|string|min:3'
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $id = DB::table('directories')->insertGetId(
            $request['data']
        );
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
            'alias' => $request['data']['alias'],
            'title' => $request['data']['title']
        ], [
            'alias' => 'required|string|max:255',
            'title' => 'required|string|min:3'
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $result = DB::table('directories')->where('id', $request['id'])->update($request['data']);
        return ['status' => 'success', 'result' => $result];
    }
 
    /**
     * Удалить данные
     *
     */
    public function delete(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $result = DB::table('directories')->where('id', $request['id'])->delete();
        $directories = DB::table('directories')->get();
        return ['status' => 'success', 'delete' => $result, 'result' => $directories];
    }  

}
