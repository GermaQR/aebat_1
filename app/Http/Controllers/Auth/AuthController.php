<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/reserva/create';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware($this->guestMiddleware(), ['except' => 'getLogout']);

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    public function getLogout(){
        
        Auth::logout();
        
        return redirect('/logout');
    }


    //Se sobreescribe el método getRegister() para comprobar si el usuario que intenta
    //realizar un nuevo registro está autorizado (es el usuario 'admin').
    public function getRegister()
    {
        
        if ( Auth::check() && (Auth::user()->name == 'admin')) {

            return $this->showRegistrationForm();

        } else {

            $message = 'Acción no autorizada';
            return view('reserva/message', ['mensaje' => $message]);
        }

    }

    public function postRegister(Request $request)
    {
        if ( Auth::user()->name != 'admin') {

            $message = 'Acción no autorizada';
            return view('reserva/message', ['mensaje' => $message]);
        } else {

            return $this->register($request);
        }
        
    }


}
