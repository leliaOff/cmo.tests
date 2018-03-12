<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MunicipalitiesController extends Controller
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
        $municipalities = DB::table('municipalities')->get();
        return ['status' => 'success', 'result' => $municipalities];
    }

    /**
     * Получить элемент
     *
     */
    public function get(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $id = $request['id'];
        $municipalities = DB::table('municipalities')->where('id', $id)->first();
        return ['status' => 'success', 'result' => $municipalities];
    }

    /**
    * Вставить данные
    *
    */
    public function insert(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $validator = Validator::make([
            'code' => $request['data']['code'],
            'name' => $request['data']['name'],
        ], [
            'code' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $id = DB::table('municipalities')->insertGetId(
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
            'code' => $request['data']['code'],
            'name' => $request['data']['name'],
        ], [
            'code' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $result = DB::table('municipalities')->where('id', $request['id'])->update($request['data']);
        return ['status' => 'success', 'result' => $result];
    }

    /**
    * Удалить данные
    *
    */
    public function delete(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        
        $count = DB::table('schools')->where('municipality_id', $request['id'])->count();
        if($count != 0) {
            return [
                'status' => 'fail',
                'error'  => 'К этому муниципалитетному образованию привязано одно или несколько образовательных организаций',
            ];
        }        
        $result = DB::table('municipalities')->where('id', $request['id'])->delete();
        $municipalities = DB::table('municipalities')->get();
        return ['status' => 'success', 'delete' => $result, 'result' => $municipalities];
    }  

}
