<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        if(!Auth::check()) return ['status' => 'relogin'];
        $tests = DB::table('tests')->where('state', '!=', 'delete')->select('id', 'name', 'datetime', 'state')->get();
        return ['status' => 'success', 'result' => $tests];
    }

    /**
     * Получить элемент
     *
     */
    public function get(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $id = $request['id'];
        $directory = DB::table('tests')->where('id', $id)->first();
        return ['status' => 'success', 'result' => $directory];
    }

    // $table->string('name');
    // $table->text('description');
    // $table->string('state')->default('draft');

    /**
    * Вставить данные
    *
    */
    public function insert(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $validator = Validator::make([
            'name' => $request['data']['name'],
            'state' => $request['data']['state'],
        ], [
            'name' => 'required|string|max:255',
            'state' => 'string|in:draft,published'
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $id = DB::table('tests')->insertGetId(
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
            'name' => $request['data']['name'],
            'state' => $request['data']['state'],
        ], [
            'name' => 'required|string|max:255',
            'state' => 'string|in:draft,published'
        ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $result = DB::table('tests')->where('id', $request['id'])->update($request['data']);
        return ['status' => 'success', 'result' => $result];
    }

    /**
    * Удалить данные
    *
    */
    public function delete(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $result = DB::table('tests')->where('id', $request['id'])->update(['state' => 'delete']);
        $tests = DB::table('tests')->where('state', '!=', 'delete')->select('id', 'name', 'datetime', 'state')->get();
        return ['status' => 'success', 'delete' => $result, 'result' => $tests];
    }  

}
