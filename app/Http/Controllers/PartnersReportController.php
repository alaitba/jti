<?php

namespace App\Http\Controllers;

use App\Exports\PartnersReport;
use App\Exports\QuizResults;
use App\Http\Utils\ResponseBuilder;
use App\Jobs\PartnersNotificationJob;
use App\Jobs\PartnersReportJob;
use App\Jobs\QuizResultsExportJob;
use App\Jobs\QuizResultsExportNotificationJob;
use App\Models\Partner;
use App\Models\PartnerAuth;
use App\Models\SalesPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
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
        $notifications = auth()->user()->notifications;

        return view('reports.partners.index', [
            'from_date' => now()->subMonth(),
            'to_date' => now(),
            'notifications' => $notifications
        ]);
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
     * @param Request $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function getList(Request $request)
    {
        $sql = <<<SQL
                id,
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

        $items = Partner::withTrashed()->select(DB::raw($sql));

        $phone = $request->input('mobile_phone', '');
        if ($phone != '')
        {
            $items->where('mobile_phone', 'like', '%' . $phone . '%');
        }

        $tradepoint = $request->input('tradepoint', '');
        if ($tradepoint != '')
        {
            $items->where('current_tradepoint', 'like', '%' . $tradepoint . '%');
        }

        $os = $request->input('os', 0);
        $android = 'Android';
        $ios = 'iOS';

        if ($os == 1) {
            $items->where('platform', 'like', '%' . $ios . '%');
        }

        if ($os == 2) {
            $items->where('platform', 'like', '%' . $android . '%');
        }

        $status = $request->input('status', 0);

        if ($status == 1) {
            $items->whereNotNull('deleted_at');
        }

        if ($status == 2) {
            $items->whereNotNull('current_tradepoint');
        }

        if ($status == 3) {
            $items->whereNull('deleted_at')
                  ->whereNull('current_tradepoint');
        }

        $fromDate = $request->input('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        if ($fromDate != '') {
            $items->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        }

        $lastFromDate = $request->input('last_from_date', now()->subMonth()->format('Y-m-d'));
        $lastToDate = $request->input('last_to_date', now()->format('Y-m-d'));

        if ($lastFromDate != '') {
            $items->whereBetween('updated_at', [$lastFromDate . ' 00:00:00', $lastToDate . ' 23:59:59']);
        }

        if ($request->input('export', 0))
        {
            $path = 'QuizResults' . now()->format('Y-m-d_H:i') . '.xlsx';

            $items->chunk(15000, function ($itemsChunks) use ($path, $request) {
                dispatch(new PartnersReportJob($itemsChunks, $path));
                dispatch(new PartnersNotificationJob($request->user()->id, $path));
            });

            return redirect()->back()->with('message', 'Начался экспорт продавцов');
        }

        $items = $items->paginate(30);

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('reports.partners.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#salesPlanTable', $itemsHtml, $pages);
        return $response->makeJson();
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
