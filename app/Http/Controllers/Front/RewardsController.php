<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\TradePointContact;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use Exception;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalance()
    {
        try {
            $result = JtiApiProvider::getBalance(auth('partners')->user()->current_uid)->getBody();
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

    public function getAvailableRewards(Request $request)
    {
        $locale = $request->input('locale', 'ru');
        try {
            $result = JtiApiProvider::getAvailableRewards(auth('partners')->user()->current_uid)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no_data'
                ], 404);
            }
            foreach ($result['resultObject'] as $key => $reward)
            {
                if ($reward['qty'] <= 0)
                {
                    unset($result['resultObject'][$key]);
                } else {
                    $dbReward = Reward::withoutTrashed()->with('photos')->where('crm_id', $reward['rewardId'])->first();
                    if (!$dbReward)
                    {
                        unset($result['resultObject'][$key]);
                    } else {
                        $result['resultObject'][$key]['price'] = $dbReward->price;
                        $result['resultObject'][$key]['totalQty'] = $dbReward->qty;
                        $result['resultObject'][$key]['name'] = $dbReward->getTranslation('name', $locale);
                        $result['resultObject'][$key]['description'] = $dbReward->getTranslation('description', $locale);
                        $result['resultObject'][$key]['images'] = $dbReward->photos->map(function ($image) {
                            return [
                                'origin_url' => $image->url,
                                'sizes' => collect($image->conversions)->map(function ($conversion) {
                                    return collect($conversion)->only(['width', 'height', 'url']);
                                })
                            ];
                        });
                    }
                }
            }
            return response()->json([
                'status' => 'ok',
                'rewards' => $result['resultObject']
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
