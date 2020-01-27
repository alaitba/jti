<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use App\Services\ValidatorService\ValidatorService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class RewardsController
 * @package App\Http\Controllers\Front
 */
class RewardsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getBalance()
    {
        try {
            $result = JtiApiProvider::getBalance(auth('partners')->user()->current_uid)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no_data'
                ], 404);
            }
            return response()->json([
                'status' => 'ok',
                'balance' => $result['resultObject']['points'] ?? 0
            ]);
        } catch (Exception $e) {
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'api_failed'
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @param Request $request
     * @return bool|JsonResponse
     */
    public function createReward(Request $request)
    {
        $rewardId = $request->input('reward_id');
        $validation = ValidatorService::validateRequest(['reward_id' => $rewardId], ['reward_id' => 'required|uuid']);
        if ($validation !== true) {
            return $validation;
        }

        try {
            $result = JtiApiProvider::createReward(auth('partners')->user()->current_uid, $rewardId)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'unavailable'
                ], 403);
            }
            return response()->json([
                'status' => 'ok'
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
