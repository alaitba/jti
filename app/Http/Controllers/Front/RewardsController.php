<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\TradePointContact;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use Exception;

class RewardsController extends Controller
{
    public function getBalance()
    {
        try {
            $tradePointContact = TradePointContact::withoutTrashed()
                ->where('account_code', auth('partners')->user()->current_tradepoint)
                ->first();
            if (!$tradePointContact)
            {
                return response()->json(['status' => 'error', 'message' => 'tradepoint_not_set'], 403);
            }

            $result = JtiApiProvider::getBalance($tradePointContact->contact_uid)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'] || !isset($result['resultObject']['points']))
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no_data'
                ], 404);
            }
            return response()->json([
                'status' => 'ok',
                'balance' => $result['resultObject']['points']
            ]);
        } catch (Exception $e) {
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'api_failed'
            ], 500);
        }

    }
}
