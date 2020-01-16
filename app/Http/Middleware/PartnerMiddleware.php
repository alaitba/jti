<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


/**
 * Class PartnerMiddleware
 * @package App\Http\Middleware
 */
class PartnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth('partners')->guest()) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
