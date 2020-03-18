<?php

namespace App\Http\Controllers;


use App\Http\Utils\ResponseBuilder;
use App\Models\PollResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Class PollReportController
 * @package App\Http\Controllers
 */
class PollReportController extends Controller
{

    /**
     * @return array|string
     * @throws Throwable
     */
    public function index()
    {
        return view('reports.polls.index')->render();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = PollResult::query()->orderBy('created_at', 'DESC')->paginate(30);
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('reports.polls.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#pollsTable', $itemsHtml, $pages);
        return $response->makeJson();
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function view($id)
    {
        $pollResult = PollResult::query()->findOrFail($id);
        $resultQuestions = collect($pollResult->questions)->keyBy('id')->toArray();
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Результаты опроса',
                        'content' => view('reports.polls.view', [
                            'quizQuestions' => $pollResult->quiz->questions,
                            'resultQuestions' => $resultQuestions
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }
}
