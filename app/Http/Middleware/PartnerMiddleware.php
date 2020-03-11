<?php

namespace App\Http\Middleware;

use App\Models\PartnerAuth;
use Closure;
use Illuminate\Http\Request;
use Browser;

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
        $request->merge(['me' => $partner]);
        if (!$setTradePoint)
        {
            PartnerAuth::query()->where('partner_id', $partner->id)->where('account_code', $partner->current_tradepoint)->update([
                'last_seen' => now(),
                'os' => Browser::platformFamily()
            ]);
        }
        return $next($request);
    }
}
