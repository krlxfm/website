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
     * Show the view for completing login for email/password users that exist.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function password(Request $request)
    {
        if(!$request->session()->has('user')) {
            return redirect()->route('login');
        }

        return view('auth.password');
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

        if(ends_with($request->input('email'), '@carleton.edu')) {
            return redirect()->route('login.carleton');
        }

        $user = User::whereEmail($request->input('email'))->first();
        $request->session()->put('email', $request->input('email'));
        $request->session()->put('user', $user);
        return redirect()->route($user == null ? 'register' : 'login.password');
    }
}
