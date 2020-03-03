<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\SalesPlan;
use App\Models\SalesPlanHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PlanFactController
 * @package App\Http\Controllers\Front
 */
class PlanFactController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function current(Request $request)
    {
        $accounts = array_keys($request->input('me')->tradepointsArray());
        /*$items = SalesPlan::with(['tobacco_brand' => function(HasOneThrough $q) {
            $q->with(['photos' => function(MorphMany $sq) {
                $sq->select('id', 'imageable_id', 'imageable_type', 'original_file_name', 'conversions', 'mime');
            }])->select('tobacco_brands.id', 'tobacco_brands.brand');
        }])->select(['sales_plans.account_code', 'bonus_portfolio', 'bonus_brand', 'plan_portfolio', 'plan_brand', 'fact_portfolio', 'fact_brand', 'sp2.brand'])
            ->whereIn('sales_plans.account_code', $accounts)
            ->leftJoin('sales_plan2s as sp2', 'sp2.account_code', '=', 'sales_plans.account_code')
            ->get()->groupBy('account_code');*/
        $items = [];
        SalesPlan::with('tobacco_brand.photos')->select(['sales_plans.account_code', 'bonus_portfolio', 'bonus_brand', 'plan_portfolio', 'plan_brand', 'fact_portfolio', 'fact_brand'])
            ->whereIn('sales_plans.account_code', $accounts)
            ->get()->each(function (SalesPlan $plan) use (&$items){
                $items[$plan->account_code] = $plan->only([
                    'bonus_portfolio',
                    'bonus_brand',
                    'plan_portfolio',
                    'plan_brand',
                    'fact_portfolio',
                    'fact_brand'
                ]);
                $items[$plan->account_code]['brand'] = $plan->tobacco_brand->brand;
                $items[$plan->account_code]['photos'] = $plan->tobacco_brand->photos->map(function (Media $media) {
                    $conversions = collect($media->conversions)->map(function ($conversion) {
                        return collect($conversion)->only(['mime', 'width', 'height', 'url']);
                    });
                    $media->setAttribute('sizes', $conversions);
                    return $media->only(['url', 'mime', 'sizes']);
                });
            });
        return response()->json([
            'status' => 'ok',
            'data' => $items
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function history(Request $request)
    {
        $accounts = array_keys($request->input('me')->tradepointsArray());
        $items = SalesPlanHistory::query()->whereIn('account_code', $accounts)
            ->whereDate('year_month', '>=', $request->input('from_date', Carbon::now()->startOfYear()))
            ->orderBy('year_month', 'DESC')
            ->get(['account_code', 'year_month', 'bonus_portfolio', 'bonus_brand', 'plan_portfolio', 'plan_brand', 'fact_portfolio', 'fact_brand'])->groupBy('account_code');
        return response()->json([
            'status' => 'ok',
            'data' => $items->toArray()
        ]);
    }
}
