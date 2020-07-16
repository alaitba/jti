<?php

namespace App\Http\Controllers;

use App\Exports\PartnerAuthExport;
use App\Http\Utils\ResponseBuilder;
use App\Models\Partner;
use App\Models\PartnerAuth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

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

        return view('reports.partners-auth.index', [
            'from_date' => now()->subMonth(),
            'to_date' => now()
        ]);
    }

    public function getAuthList(Request $request)
    {
        $items = PartnerAuth::query()
            ->join('partners', 'partner_auths.partner_id', '=', 'partners.id')
            ->join('contacts', 'partner_auths.contact_uid', '=', 'contacts.contact_uid')
            ->join('trade_points', 'partner_auths.account_code', '=', 'trade_points.account_code')
            ->join('trade_agents', 'trade_points.employee_code', '=', 'trade_agents.employee_code')
            ->select( DB::raw('CONCAT(contacts.first_name, " ", contacts.middle_name, " ", contacts.last_name) AS name'),
                'partners.mobile_phone',
                'partner_auths.account_code',
                'trade_agents.employee_name',
                'partner_auths.login',
                'partner_auths.last_seen',
                'partner_auths.os',
                'partner_auths.ip');

        $phone = $request->input('mobile_phone', '');

        if ($phone != '')
        {
            $items->where('partners.mobile_phone', 'like', '%' . $phone . '%');
        }

        $tradepoint = $request->input('tradepoint', '');

        if ($tradepoint != '')
        {
            $items->where('partner_auths.account_code', 'like', '%' . $tradepoint . '%');
        }

        $fromDate = $request->input('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        if ($fromDate != '') {
            $items->whereBetween('partner_auths.login', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        if ($request->input('export', 0))
        {
            ini_set('memory_limit', '-1');
            return Excel::download(new PartnerAuthExport($items->get()), 'QuizResults.xlsx');
        }

        $items = $items->paginate(25);

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('reports.partners-auth.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#quizzesTable', $itemsHtml, $pages);
        return $response->makeJson();
    }
}
