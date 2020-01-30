<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PartnersReportController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('reports.partners.index');
    }

    /**
     * @return JsonResponse
     */
    public function getList()
    {
        $sql = <<<SQL
                current_tradepoint,
                mobile_phone,
                created_at,
                updated_at,
                CASE
                    WHEN deleted_at IS NOT NULL THEN 'Deleted'
                    WHEN current_tradepoint IS NOT NULL THEN 'Verified'
                    ELSE 'Not verified'
                END as status
SQL;

        $partners = Partner::withTrashed()->select(DB::raw($sql))->get();

        return response()->json(['data' => $partners]);
    }
}
