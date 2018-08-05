<?php

namespace KRLX\Http\Controllers;

use Illuminate\Http\Request;

class PointController extends Controller
{
    /**
     * View the membership contract.
     *
     * @return Illuminate\Http\Response
     */
    public function contract()
    {
        return view('legal.contract');
    }
}
