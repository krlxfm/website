<?php

namespace KRLX\Http\Controllers\Auth;

use Illuminate\Http\Request;
use KRLX\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Override the logic where we show the form.
     * Users will have already entered their email address, so there's no need
     * to make them enter it again. As such we'll immediately send the reset
     * email and send the user back to the main screen.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Redirect
     */
    public function showLinkRequestForm(Request $request)
    {
        if (! $request->session()->has('email')) {
            return redirect()->route('login');
        }

        $requestWithData = $request->merge(['email' => $request->session()->get('email')]);

        $request->session()->forget('user');
        $request->session()->forget('email');

        return $this->sendResetLinkEmail($requestWithData);
    }
}
