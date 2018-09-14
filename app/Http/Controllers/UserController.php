<?php

namespace KRLX\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a page where users can edit their profile and
     * account information.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        return view('account.profile', ['user' => $request->user()]);
    }
}
