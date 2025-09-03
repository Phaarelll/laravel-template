<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('content.front-pages.landing');
    }
}
