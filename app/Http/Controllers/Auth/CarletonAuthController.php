<?php

namespace KRLX\Http\Controllers\Auth;

use Socialite;
use KRLX\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use KRLX\Http\Controllers\Controller;

class CarletonAuthController extends Controller
{
    /**
     * Redirects users to the Carleton authentication page.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function redirect(Request $request)
    {
        if(!$request->session()->has('user')) {
            return redirect()->route('login');
        }

        return Socialite::driver('google')->with(['hd' => 'carleton.edu'])->redirect();
    }

    /**
     * Process details about the authenticating user from Google, and redirect
     * to the intended destination.
     *
     * @return Illuminate\Http\Response
     */
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
        $user = User::whereEmail($googleUser->getEmail())->first();

        if(!$user) {
            $netid = explode('@', $googleUser->getEmail())[0];
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'photo' => config('defaults.directory').$netid,
                'password' => Hash::make($googleUser->getId().config('defaults.salt'))
            ]);
        }

        Auth::login($user, true);
        return redirect()->intended('/home');
    }
}