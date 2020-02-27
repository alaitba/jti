<?php

namespace App\Http\Controllers\Front;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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

}
