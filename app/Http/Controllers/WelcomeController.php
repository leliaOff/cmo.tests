<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RegistersUsers;

class WelcomeController extends Controller
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
    public function index(Request $request)
    {
        if(!$this->checkSupport()) {
            return view('browser');
        }
        
        if ($request->session()->has('session_key')) {
            $key = $request->session()->get('session_key');
        } else {
            $key = sha1(time() . rand(1000, 9999));
            $request->session()->put('session_key', $key);
        }
        return view('welcome', ['key' => $key]);
    }

    /**
     * Браузер пользователя
     *
     * @return void
     */
    public function checkSupport()
    {
        $userAgent = $_SERVER["HTTP_USER_AGENT"];        
        preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon|Edge)(?:\/| )([0-9.]+)/", $userAgent, $browserInfo);
        
        if(empty($browserInfo)) return false;
        
		list(, $browser, $version) = $browserInfo;
		if ($browser == 'Opera' && $version == '9.80') {
			$version == substr($userAgent, -5);
		}
		if ($browser == 'Version') {
			$browser = 'Safari';
		}
		if (!$browser && strpos($userAgent, 'Gecko')) {
			$browser = 'Gecko';
		}
		//Иначе эдж распознается как хром
        if (strpos($userAgent, 'Edge')) {
            $browser = 'Edge';
        }

		if ($browser != 'Firefox' && $browser != 'Chrome' && $browser != 'Opera' && $browser != 'Safari') {
			return false;
		} elseif ($browser == 'Chrome' && $version < 50) {
			return false;
		} elseif ($browser == 'Firefox' && $version < 45) {
			return false;
		} elseif ($browser == 'Opera' && $version < 42) {
			return false;
		} elseif ($browser == 'Safari' && $version < 5.1) {
			return false;
		}
		return true;
    }

    /**
     * Login.
     *
     * @return void
     */
    public function login(Request $request)
    {
        
        if(!Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            return [
                'status' => 'fail',
                'error' => 'Неверное регистрационное имя или пароль'
            ];
        }

        $user = Auth::user();
        $id = Auth::id();

        return [
            'status' => 'success',
            'result'   => $user
        ];
    }

    /**
     * Registration.
     *
     * @return void
     */
     public function registration(Request $request)
     {
        
        $validator = Validator::make([
                'email' => $request['email'],
                'password' => $request['password'],
                'password_confirmation' => $request['repassword'],
            ], [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed'
            ]);

        if($validator->fails()) {
            
            return [
                'status' => 'fail',
                'error'  => $validator->messages(),
            ];

        }

        $user = User::create([
            'name' => $request['email'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        return [
            'status' => 'success',
            'result' => $user,
        ];
     }

    /**
     * @brief Метод для выхода
     */
    public function logout() {
        
        Auth::logout();
        
    }

}
