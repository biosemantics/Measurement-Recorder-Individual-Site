<?php

namespace App\Http\Controllers\Api\V1;

use App\Specimen;
use App\ActionLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class HomeController extends Controller
{
    //
    public function store(Request $request)
    {
        $specimen = Specimen::create($request->all());
        return $specimen;
    }

    public function log(Request $request)
    {
        $actionLog = ActionLog::create($request->all());
        return $actionLog;
    }

    public function all(Request $request)
    {
        $all = Specimen::all();
        return $all;
    }
}
