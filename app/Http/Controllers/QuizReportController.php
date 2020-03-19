<?php

namespace App\Http\Controllers;



use App\Http\Utils\ResponseBuilder;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        return view('reports.quizzes.index')->render();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = QuizResult::query()->orderBy('created_at', 'DESC')->paginate(30);
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
