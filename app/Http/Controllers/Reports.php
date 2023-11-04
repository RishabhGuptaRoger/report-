<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Reports extends Controller
{
    public function index(Request $request)
    {
        return view('layouts.reports');
    }

}
