<?php

namespace KRLX\Http\Controllers\API;

use Illuminate\Http\Request;
use KRLX\Http\Controllers\Controller;
use KRLX\User;

class UserController extends Controller
{
    /**
     * Search the database for users with the specified name or email address.
     *
     * @return Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $request->validate(['query' => 'required']);
        $emails = User::where('email', 'like', '%'.$request->input('query').'%')->get();
        $names = User::where('name', 'like', '%'.$request->input('query').'%')->get();

        return $emails->concat($names)->unique()->sortBy('email');
    }
}
