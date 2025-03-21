<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
   
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}
