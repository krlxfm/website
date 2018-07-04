<?php

namespace KRLX\Http\Controllers\Auth;

use KRLX\User;
use Illuminate\Http\Request;
use KRLX\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as defaultLogin;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Process "pre-login" requests and send the user to the correct location
     * based on the account type.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if($request->has('password')) {
            return $this->defaultLogin($request);
        }

        $request->validate([
            'email' => 'required|email',
            'terms' => 'accepted'
        ]);

        $user = User::whereEmail($request->input('email'))->first();
        $request->session()->put('user', $request->input('email'));
        return redirect()->route($user ? 'login' : 'register');
    }
}
