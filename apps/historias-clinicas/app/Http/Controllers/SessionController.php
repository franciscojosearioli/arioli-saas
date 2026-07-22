<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function renewSession(Request $request)
    {
        return response()->json(['success' => true]);
    }
}
