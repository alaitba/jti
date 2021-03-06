<?php

namespace App\Http\Controllers\Front;


use App\Models\Partner;
use App\Models\SalesPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class PurchaseController
 * @package App\Http\Controllers\Front
 */
class PurchaseController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveDays(Request $request)
    {
        $accounts = array_keys($request->input('me')->tradepointsArray());
        $tradePoint = $request->input('tradepoint');
        if (!in_array($tradePoint, $accounts))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid_tradepoint'
            ], 403);
        }
        /** @var Partner $partner */
        $partner = auth('partners')->user();
        $partner->purchase_weekdays()->updateOrCreate([
            'tradepoint' => $tradePoint
        ], [
            'weekdays' => $request->input('weekdays', [])
        ]);
        return response()->json([
            'status' => 'ok',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPlanFact(Request $request)
    {
        $accounts = array_keys($request->input('me')->tradepointsArray());
        $tradePoint = $request->input('tradepoint');
        if (!in_array($tradePoint, $accounts))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid_tradepoint'
            ], 403);
        }

        $planFact = SalesPlan::query()->where('account_code', $tradePoint)->first();
        return response()->json([
            'status' => 'ok',
            'plan' => $planFact->plan_portfolio ?? null,
            'fact' => $planFact->fact_portfolio ?? null,
        ]);
    }

}
