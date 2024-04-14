<?php

namespace App\Http\Middleware;

use Closure;

class HandleOptionsRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')) {
            return response()->json('', 200, [
                'Access-Control-Allow-Origin' => 'https://www.vangoo.mx',
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization, X-Requested-With, Application, Cache-Control',
            ]);
        }

        return $next($request);
    }
}
