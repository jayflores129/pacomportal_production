<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

use App\Models\Login

class LogController extends Controller
{
    
    public function __construct(Request $request) 
    {
    	$this->request = $request;
    }

    public function handle(Login $event) 
    {
    	        $log = new Login();
                $log->company = 4;
                $log->save();
    }
}
