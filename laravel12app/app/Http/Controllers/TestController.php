<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function message()
    {
        return response()->json([
            'message' => 'Laravel API is working 🚀'
        ]);
    }
}
