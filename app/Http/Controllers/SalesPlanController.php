<?php

namespace App\Http\Controllers;

use App\Http\Utils\ResponseBuilder;
use App\Models\ImportHistory;
use App\Models\SalesPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SalesPlanController extends Controller
{

    /**
     * @return array|string
     * @throws Throwable
     */
    public function index()
    {
        $updated = ImportHistory::where('failed', 0)->where('type', 'SalesPlan')->orderBy('id', 'DESC')->first();
        return view('reports.sales_plan.index', [
            'lastUpdate' => $updated->created_at_string ?? '-'
        ])->render();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = SalesPlan::paginate(30);
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('reports.sales_plan.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#salesPlanTable', $itemsHtml, $pages);
        return $response->makeJson();
    }
}
