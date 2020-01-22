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
     * @param Request $request
     * @param Closure $next
     * @param bool $setTradePoint
     * @return mixed
     */
    public function handle($request, Closure $next, $setTradePoint = false)
    {
        if (auth('partners')->guest()) {
            return response('Unauthorized.', 401);
        }

        $partner = auth('partners')->user();
        if (!$partner->current_uid && !$setTradePoint) {
            $tradepoints = $partner->tradepointsArray();
            return response()->json([
                'status' => 'error',
                'message' => 'need_tradepoint',
                'tradepoints' => $tradepoints
            ], 403);
        }

        return $next($request);
    }
}
