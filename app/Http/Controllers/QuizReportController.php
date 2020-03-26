<?php

namespace App\Http\Controllers;



use App\Exports\QuizResults;
use App\Http\Utils\ResponseBuilder;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Class QuizReportController
 * @package App\Http\Controllers
 */
class QuizReportController extends Controller
{

    /**
     * @return array|string
     * @throws Throwable
     */
    public function index()
    {
        return view('reports.quizzes.index', [
            'quizzes' => Quiz::withTrashed()->where('type', 'quiz')->get(),
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
        $items = QuizResult::query()->orderBy('created_at', 'DESC');
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

        $quizId = $request->input('quiz_id', 0);
        if ($quizId > 0)
        {
            $items->where('quiz_id', $quizId);
        }

        $success = $request->input('success', 0);
        if ($success > 0)
        {
            $items->where('success', $success - 1);
        }

        $fromDate = $request->input('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $items->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
        if ($request->input('export', 0))
        {
            return Excel::download(new QuizResults($items->get()), 'QuizResults.xlsx');
        }

        $items = $items->paginate(30);

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('reports.quizzes.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#quizzesTable', $itemsHtml, $pages);
        return $response->makeJson();
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function view($id)
    {
        $quizResult = QuizResult::query()->findOrFail($id);
        $resultQuestions = collect($quizResult->questions)->keyBy('id')->toArray();
        $items = [];
        /** @var QuizQuestion $question */
        foreach ($quizResult->quiz->questions as $question)
        {
            $answer = QuizAnswer::query()->find($resultQuestions[$question->id]['answer']);
            $items []= [
                'question' => $question->question ?? '-',
                'answer' => $answer->answer,
                'correct' => $answer->correct
            ];
        }
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Результаты викторины',
                        'content' => view('reports.quizzes.view', [
                            'items' => $items
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }
}
