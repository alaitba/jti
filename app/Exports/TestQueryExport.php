<?php


namespace App\Exports;

use App\Models\QuizResult;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Excel;

class TestQueryExport implements FromQuery, Responsable
{
    use Exportable;

//    private $quizResults;
//
//    public function __construct($request)
//    {
//        $this->request = $request;
//    }

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'invoices.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    public function query()
    {
        return QuizResult::query();
//            ->join('partners', 'quiz_results.partner_id', '=', 'partners.id')
//            ->join('contacts', 'partners.current_uid', '=', 'contacts.contact_uid')
//            ->join('quizzes', 'quiz_results.quiz_id', '=', 'quizzes.id')
//            ->join('quiz_questions', 'quizzes.id', '=', 'quiz_questions.quiz_id')
//            ->join('quiz_answers', 'quiz_questions.id', '=', 'quiz_answers.quiz_question_id')
         /*   ->select('quiz_results.id',
                DB::raw('CONCAT(contacts.first_name, \' \', contacts.last_name, \' \', contacts.middle_name) as Name'),
                'partners.mobile_phone', 'quizzes.title->ru as quiz',
                'quiz_results.created_at', 'quiz_results.amount', 'quiz_questions.question->ru as question',
                'quiz_answers.answer->ru as answer',
                DB::raw('(CASE WHEN quiz_answers.correct = 1 THEN "Да" ELSE "Нет" END) AS corrects'));*/
//            ->orderBy('quiz_results.created_at', 'DESC');

//        $fromDate = $this->request->input('from_date', now()->subMonth()->format('Y-m-d'));
//        $toDate = $this->request->input('to_date', now()->format('Y-m-d'));
//
//        $items->whereBetween('quiz_results.created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
    }
}
