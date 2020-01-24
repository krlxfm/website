<?php

namespace KRLX\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use KRLX\Http\Controllers\Controller;
use KRLX\User;
use Socialite;

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
        if (! $request->session()->has('email')) {
            return redirect()->route('login');
        }

        return Socialite::driver('google')->with([
            'hd' => 'carleton.edu',
            'prompt' => 'select_account',
            'login_hint' => $request->session()->get('email'),
        ])->redirect();
    }

    /**
     * Process details about the authenticating user from Google, and redirect
     * to the intended destination.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();
        $user = User::whereEmail($googleUser->getEmail())->first();

        if (! $user) {
            $netid = explode('@', $googleUser->getEmail())[0];
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'photo' => config('defaults.directory').$netid,
                'password' => Hash::make($googleUser->getId().config('defaults.salt')),
            ]);
        } elseif (! $user->password or strlen($user->password) == 0) {
            $user->password = Hash::make($googleUser->getId().config('defaults.salt'));
            $user->save();
        }

        $request->session()->forget('user');
        $request->session()->forget('email');

        Auth::login($user, true);

        return redirect()->intended('/home');
    }
}
