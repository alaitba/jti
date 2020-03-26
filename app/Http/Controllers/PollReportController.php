<?php

namespace App\Http\Controllers;


use App\Exports\PollResults;
use App\Http\Utils\ResponseBuilder;
use App\Models\PollResult;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
        return view('reports.polls.index', [
            'polls' => Quiz::withTrashed()->where('type', 'poll')->get(),
            'from_date' => now()->subMonth(),
            'to_date' => now(),
        ])->render();
    }

    /**
     * @param Request $request
     * @return JsonResponse|BinaryFileResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = PollResult::query()->orderBy('created_at', 'DESC');
        $name = $request->input('name', '');
        if ($name != '')
        {
            $items->whereHas('partner.current_contact', function(Builder $q) use ($name) {
                $q->whereRaw('CONCAT(first_name, " ", middle_name, " ", last_name) LIKE "%' . $name . '%"');
            });
        }

        $phone = $request->input('mobile_phone', '');
        if ($phone != '')
        {
            $items->whereHas('partner', function(Builder $q) use ($phone) {
                $q->where('mobile_phone', 'like', '%' . $phone . '%');
            });
        }

        $pollId = $request->input('poll_id', 0);
        if ($pollId > 0)
        {
            $items->where('quiz_id', $pollId);
        }

        $fromDate = $request->input('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $items->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        if ($request->input('export', 0))
        {
            return Excel::download(new PollResults($items->get()), 'PollResults.xlsx');
        }

        $items = $items->paginate(30);
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
        $items = [];
        /** @var QuizQuestion $question */
        foreach ($pollResult->quiz->questions as $question)
        {
            if ($question->type == 'text')
            {
                $answer = $resultQuestions[$question->id]['answer'] ?? '';
            } else {
                $ids = is_array($resultQuestions[$question->id]['answer'])
                    ? $resultQuestions[$question->id]['answer']
                    : explode(',', $resultQuestions[$question->id]['answer']);
                $answer = QuizAnswer::query()
                    ->whereIn('id', $ids)
                    ->get('answer');
            }
            $items []= [
                'question' => $question->question ?? '-',
                'answer' => $answer
            ];
        }
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Результаты опроса',
                        'content' => view('reports.polls.view', [
                            'items' => $items
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }
}
