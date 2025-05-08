<?php

namespace App\Http\Controllers\Csrf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CsrfController extends Controller
{
    public function show(Request $request)
    {
        if (!in_array($request->header('Origin'), config('cors.allowed_origins'))) {
            abort(403, 'Origen no permitido');
        }

        return response()->json([
            'csrf_token' => csrf_token(),
            'expires_in' => config('session.lifetime') * 60
        ])->header('X-CSRF-TOKEN', csrf_token());
    }
}
