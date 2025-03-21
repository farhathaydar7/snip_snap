<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class HealthCheckController extends Controller
{

    public function test()
    {
        return response()->json(['message' => 'API is working!']);
    }
}
