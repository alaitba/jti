<?php

namespace App\Console\Commands;

use App\Models\QuizAnswer;
use App\Models\QuizResult;
use App\Services\LogService\LogService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class QuizResultsExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:quiz-results-export {fromDate} {toDate} {quiz_id*} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Экспорт Инициирован');
        try {
            $items = QuizResult::query()->orderBy('created_at', 'DESC');

            $items->whereHas('quiz', function(Builder $q) {
                $q->where('deleted_at', NULL);
            });

            $quizId = $this->argument('quiz_id');

            if ($quizId > 0 && $quizId[0] > 0)
            {
                $items->whereIn('quiz_id', $quizId);
            }

            $fromDate = $this->argument('fromDate');
            $toDate = $this->argument('toDate');

            $items->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);

            $quizResultsPesults = $items->get();

            foreach ($quizResultsPesults as $quizResult) {

                $name = $quizResult->partner->current_contact->name ?? '-';
                $phone = $quizResult->partner->mobile_phone ?? '-';
                $quiz = $quizResult->quiz_with_trash->title ?? '-';
                $quizDate = $quizResult->created_at->format('d.m.Y H:i');

                $resultQuestions = collect($quizResult->questions)->keyBy('id')->toArray();

                foreach ($quizResult->quiz_with_trash->questions as $question)
                {
                    $answer = QuizAnswer::query()->find($resultQuestions[$question->id]['answer']);

                    $quizResultsExport = \App\QuizResultsExport::query()->create([
                        'id' => $quizResult->id,
                        'name' => $name,
                        'phone' => $phone,
                        'quiz' => $quiz,
                        'date' => $quizDate,
                        'bonus' => $quizResult->amount,
                        'question' => $question->question ?? '-',
                        'answer' => optional($answer)->getTranslation('answer', 'ru'),
                        'correct' => optional($answer)->correct ? 'Да' : 'Нет'
                    ]);

                    $quizResultsExport->save();
                }
            }

            $this->info('Экспорт Успешно Завершен!');
        } catch (Exception $e) {
            LogService::logException($e);
            $this->error($e->getMessage());
        }
    }
}
