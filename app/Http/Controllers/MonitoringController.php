<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Download;
use DB;


class MonitoringController extends Controller
{
    
    public function index()
    {
    	$downloads = DB::table('downloads')->orderBy('created_at', 'DESC')->paginate(30);

        $pagination = json_decode(
            json_encode($downloads)
        )->links;

    	return view('monitoring/index')->with(['downloads' => $downloads, 'pagination' => $pagination]);
    }
}
