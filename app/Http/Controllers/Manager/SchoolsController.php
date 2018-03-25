<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchoolsController extends Controller
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
        $schools = DB::table('schools')->get();
        return ['status' => 'success', 'result' => $schools];
    }

    /**
     * Получить элемент
     *
     */
    public function get(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $id = $request['id'];
        $schools = DB::table('schools')->where('id', $id)->first();
        return ['status' => 'success', 'result' => $schools];
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
            'municipality_id' => $request['data']['municipality_id'],
            'name' => $request['data']['name'],
            'INN' => $request['data']['INN'],
        ], [
            'code' => 'required|integer',
            'municipality_id' => 'required|integer|exists:municipalities,id',
            'name' => 'required|string|max:255',
            'INN' => 'max:12',
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $id = DB::table('schools')->insertGetId(
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
            'municipality_id' => $request['data']['municipality_id'],
            'name' => $request['data']['name'],
            'INN' => $request['data']['INN'],
        ], [
            'code' => 'required|integer',
            'municipality_id' => 'required|integer|exists:municipalities,id',
            'name' => 'required|string|max:255',
            'INN' => 'max:12',
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $result = DB::table('schools')->where('id', $request['id'])->update($request['data']);
        return ['status' => 'success', 'result' => $result];
    }

    /**
    * Удалить данные
    *
    */
    public function delete(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $result = DB::table('schools')->where('id', $request['id'])->delete();
        $schools = DB::table('schools')->get();
        return ['status' => 'success', 'delete' => $result, 'result' => $schools];
    }  

}
