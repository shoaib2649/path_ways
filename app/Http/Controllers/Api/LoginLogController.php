<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Http\Resources\LoginLogResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());



        $logs = LoginLog::get();

        return LoginLogResource::collection($logs);
    }
}
