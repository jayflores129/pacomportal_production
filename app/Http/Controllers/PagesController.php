<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    //

    public function registered() 
    {
    	return view('pages/registration-success');
    }

    public function serialNo() 
    {
        return view('pages/sample-rma');
    }
}
