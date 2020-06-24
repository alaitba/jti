<?php

namespace App\Http\Controllers;



use App\Exports\QuizResults;
use App\Exports\TestQueryExport;
use App\Http\Utils\ResponseBuilder;
use App\Jobs\QuizResultsExportJob;
use App\Jobs\QuizResultsExportNotificationJob;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use App\Notifications\PaymentReceive;
use App\Notifications\QuizResultsExportNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
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
        $notifications = auth()->user()->notifications;

        return view('reports.quizzes.index', [
            'quizzes' => Quiz::where('type', 'quiz')->get(),
            'from_date' => now()->subMonth(),
            'to_date' => now(),
            'notifications' => $notifications
        ])->render();
    }


    public function download()
    {
        dd('test');
    }
    /**
     * @param Request $request
     * @return JsonResponse|BinaryFileResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
//        dd($request->all());
        ini_set('memory_limit', '-1');
        $items = QuizResult::query()->orderBy('created_at', 'DESC');
            /*->join('partners', 'quiz_results.partner_id', '=', 'partners.id')
            ->join('contacts', 'partners.current_uid', '=', 'contacts.contact_uid')
            ->join('quizzes', 'quiz_results.quiz_id', '=', 'quizzes.id')
            ->join('quiz_questions', 'quizzes.id', '=', 'quiz_questions.quiz_id')
            ->join('quiz_answers', 'quiz_questions.id', '=', 'quiz_answers.quiz_question_id')
            ->select('quiz_results.id',
                             DB::raw('CONCAT(contacts.first_name, \' \', contacts.last_name, \' \', contacts.middle_name) as Name'),
                             'partners.mobile_phone', 'quizzes.title->ru as quiz',
                             'quiz_results.created_at', 'quiz_results.amount', 'quiz_questions.question->ru as question',
                             'quiz_answers.answer->ru as answer',
                             DB::raw('(CASE WHEN quiz_answers.correct = 1 THEN "Да" ELSE "Нет" END) AS corrects'))
            ->orderBy('quiz_results.created_at', 'DESC');

        $fromDate = $request->input('from_date', now()->subMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $items->whereBetween('quiz_results.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
*/

        /*, 'name' , 'mobile_phone', 'title', 'created_at'*/
//dd($items->toSql());
//dd($items->get());
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
//            dd('test');
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
//        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');


//dd($notifications);
        if ($request->input('export', 0))
        {
//            $file = 'QuizResults-2020-06-24_11:14.xlsx';
//            $path = public_path($file);
//            dd($path);
//            \Illuminate\Support\Facades\Notification::send(request()->user(), new QuizResultsExportNotification(900));
//            request()->user()->notify(new QuizResultsExportNotification(900));
            dispatch(new QuizResultsExportJob($items->get()));
            dispatch(new QuizResultsExportNotificationJob($request->user()->id));
//            return back();
//            return (new TestQueryExport())->download('testpest.xlsx');

//            $resultQuestions = collect($items->first()->questions)->keyBy('id')->toArray();
//            $quizResultsQuizQuestions = $items->first()->quiz_with_trash->questions;
//            $resultQuestions[$question->id]['answer']
//            dd($resultQuestions[73]['answer']);
//            (new TestQueryExport())->queue('QuizResults.xlsx');
//            Excel::store(new QuizResults($items->get()), 'QuizResults-' . now()->format('Y-m-d_H:i') . '.xlsx');
            return back()->withSuccess('Export started!');
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
