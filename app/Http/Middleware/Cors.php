<?php

namespace App\Http\Middleware;

use Closure;

class Cors {

    /**
     * 
     * @param type $request
     * @param Closure $next
     * @return type
     */
    public function handle($request, Closure $next) {
        return $next($request)
                        ->header('Access-Control-Allow-Origin', '*')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Cache-Control, Accept, Origin, X-Session-ID')
                        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                        ->header('Access-Control-Expose-Headers', 'Content-Type, Authorization');
    }

}
