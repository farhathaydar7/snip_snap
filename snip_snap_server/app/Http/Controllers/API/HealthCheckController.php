<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class HealthCheckController extends Controller
{
    /**
     * Check if the API is working
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        return response()->json(['message' => 'API is working!']);
    }
}
