<?php

namespace App\Http\Controllers;



use App\Http\Utils\ResponseBuilder;
use App\Jobs\QuizResultsExportJob;
use App\Jobs\QuizResultsExportNotificationJob;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $notifications = auth()->user()->notifications;

        return view('reports.quizzes.index', [
            'quizzes' => Quiz::where('type', 'quiz')->get(),
            'from_date' => now()->subMonth(),
            'to_date' => now(),
            'notifications' => $notifications
        ])->render();
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
//        dd($path);
    }

    /**
     * @param Request $request
     * @return JsonResponse|BinaryFileResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        ini_set('memory_limit', '-1');
        $items = QuizResult::query()->orderBy('created_at', 'DESC');

        $items->whereHas('quiz', function(Builder $q) {
            $q->where('deleted_at', NULL);
        });

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
        if ($quizId > 0 && $quizId[0] > 0)
        {
            $items->whereIn('quiz_id', $quizId);
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
            $path = 'QuizResults' . now()->format('Y-m-d_H:i') . '.xlsx';
            $items->chunk(5000, function ($itemsChunks) use ($path, $request) {
                dispatch(new QuizResultsExportJob($itemsChunks, $path));
                dispatch(new QuizResultsExportNotificationJob($request->user()->id, $path));
            });

            return redirect()->back()->with('message', 'Начался экспорт викторин');
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
