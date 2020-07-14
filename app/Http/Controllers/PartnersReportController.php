<?php

namespace App\Http\Controllers;

use App\Http\Utils\ResponseBuilder;
use App\Jobs\PartnerAuthJob;
use App\Jobs\PartnerAuthNotificationJob;
use App\Jobs\PartnerExportJob;
use App\Models\Partner;
use App\Models\PartnerAuth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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


    public function download($path)
    {
        if (file_exists(storage_path('app/' . $path))) {
            return response()->download(storage_path('app/' . $path));
        } else {
            return redirect()->back()->with('message', 'Ошибка файла нет');
        }
    }

    public function delete($id)
    {
        $notifications = \App\Models\Notification::find($id);
        $file_path = storage_path('app/' . json_decode($notifications->data)->path);
        if (file_exists(storage_path('app/' . json_decode($notifications->data)->path))) {
            unlink($file_path);
        }
        $notifications->delete();
        return redirect()->back()->with('message', 'Файл ' . json_decode($notifications->data)->path . ' Удален');
    }

    /**
     * @return Factory|View
     */
    public function auth()
    {
        $notifications = auth()->user()->notifications;

        return view('reports.partners-auth.index', [
            'from_date' => now()->subMonth(),
            'to_date' => now(),
            'notifications' => $notifications
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function getAuthList(Request $request)
    {
        ini_set('memory_limit', '-1');

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

        $items->whereBetween('partner_auths.login', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

        if ($request->input('export', 0))
        {
            $path = 'PartnerAuth' . now()->format('Y-m-d_H:i') . '.xlsx';
            $items->chunk(5000, function ($itemsChunks) use ($path, $request) {
                dispatch(new PartnerAuthJob($itemsChunks, $path));
                dispatch(new PartnerAuthNotificationJob($request->user()->id, $path));
            });

            return redirect()->back()->with('message', 'Начался экспорт викторин');
        }

        $items = $items->paginate(30);

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
