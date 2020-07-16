<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerAuth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class PartnersReportController
 * @package App\Http\Controllers
 */
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
                platform,
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


    /**
     * @return Factory|View
     */
    public function auth()
    {
        return view('reports.partners-auth.index');
    }

    public function getAuthList()
    {
        $sql = <<<SQL
SELECT
    CONCAT(c.first_name, " ", c.middle_name, " ", c.last_name) AS name,
    p.mobile_phone,
    pa.account_code,
    ta.employee_name AS trade_agent,
    pa.login,
    pa.last_seen,
    pa.os,
    pa.ip
FROM `partner_auths` AS pa, `contacts` AS c, `partners` AS p, `trade_points` AS tp, `trade_agents` AS ta
WHERE pa.partner_id=p.id AND c.contact_uid=pa.contact_uid AND tp.account_code=pa.account_code AND tp.employee_code=ta.employee_code
SQL;
        $data = PartnerAuth::query()->fromQuery($sql);
        return response()->json(['data' => $data]);
    }
}
