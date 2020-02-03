<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\SalesPlan;
use App\Models\SalesPlanHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanFactController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function current(Request $request)
    {
        $accounts = array_keys($request->input('me')->tradepointsArray());
        $items = SalesPlan::query()
            ->select(['sales_plans.account_code', 'bonus_portfolio', 'bonus_brand', 'plan_portfolio', 'plan_brand', 'fact_portfolio', 'fact_brand', 'sp2.brand'])
            ->whereIn('sales_plans.account_code', $accounts)
            ->leftJoin('sales_plan2s as sp2', 'sp2.account_code', '=', 'sales_plans.account_code')
            ->get()->groupBy('account_code');
        return response()->json([
            'status' => 'ok',
            'data' => $items->toArray()
        ]);
    }

    public function history(Request $request)
    {
        $accounts = array_keys($request->input('me')->tradepointsArray());
        $items = SalesPlanHistory::query()->whereIn('account_code', $accounts)
            ->whereDate('year_month', '>=', $request->input('from_date', Carbon::now()->startOfMonth()->subMonths(3)))
            ->orderBy('year_month', 'DESC')
            ->get(['account_code', 'year_month', 'bonus_portfolio', 'bonus_brand', 'plan_portfolio', 'plan_brand', 'fact_portfolio', 'fact_brand'])->groupBy('account_code');
        return response()->json([
            'status' => 'ok',
            'data' => $items->toArray()
        ]);
    }
}
