<?php

namespace App\Http\Controllers\Manager;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
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
        if(!Auth::check()) return ['status' => 'relogin'];
        $users = DB::table('users')->select('id', 'role', 'name')->get();
        return ['status' => 'success', 'result' => $users];
    }

    /**
    * Получить элемент
    *
    */
    public function get(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $id = $request['id'];
        $directory = DB::table('users')->select('id', 'role', 'email')->where('id', $id)->first();
        return ['status' => 'success', 'result' => $directory];
    }

    /**
    * Вставить данные
    *
    */
    public function insert(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $validator_fields = [
            'email' => $request['data']['email'],
            'role' => $request['data']['role'],
            'password' => $request['data']['password'],
            'password_confirmation' => $request['data']['confirmation'],
        ];
        $validator_rules = [
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:user,admin',
            'password' => 'required|string|min:6|confirmed',
        ];
        $validator = Validator::make($validator_fields, $validator_rules);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $id = DB::table('users')->insertGetId([
            'name' => $request['data']['email'],
            'email' => $request['data']['email'],
            'role' => $request['data']['role'],
            'password' => bcrypt($request['data']['password']),
        ]);
        return ['status' => 'success', 'result' => $id];
    }

    /**
    * Изменить данные
    *
    */
    public function update(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];

        $validator_fields = [
            'email' => $request['data']['email'],
            'role' => $request['data']['role']
        ];
        $validator_rules = [
            'email' => 'required|string|email|max:255|unique:users,email,' . $request['id'],
            'role' => 'required|string|in:user,admin'
        ];
        if(isset($request['data']['password']) && $request['data']['password'] != '') {
            $validator_fields['password'] = $request['data']['password'];
            $validator_fields['password_confirmation'] = $request['data']['confirmation'];
            $validator_rules['password'] = 'required|string|min:6|confirmed';
        }
        $validator = Validator::make($validator_fields, $validator_rules);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $data = [
            'name' => $request['data']['email'],
            'email' => $request['data']['email'],
            'role' => $request['data']['role']
        ];
        if(isset($request['data']['password']) && $request['data']['password'] != '') {
            $data['password'] = bcrypt($request['data']['password']);
        }
        $result = DB::table('users')->where('id', $request['id'])->update($data);
        
        return ['status' => 'success', 'result' => $result];
    }

    /**
    * Удалить данные
    *
    */
    public function delete(Request $request)
    {
        if(!Auth::check()) return ['status' => 'relogin'];
        $result = DB::table('users')->where('id', $request['id'])->delete();
        $users = DB::table('users')->get();
        return ['status' => 'success', 'delete' => $result, 'result' => $users];
    } 

}
