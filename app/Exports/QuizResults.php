<?php

namespace App\Exports;

use App\Models\QuizAnswer;
use App\Models\QuizResult;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;


class QuizResults implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $quizResults;

    public function __construct($quizResults)
    {
        $this->quizResults = $quizResults;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $items = [];
        /** @var QuizResult $quizResult */
        foreach ($this->quizResults as $quizResult) {
//            $name = $quizResult->partner->current_contact->name ?? '-'; //ready
//            $phone = $quizResult->partner->mobile_phone ?? '-'; //ready
//            $quiz = $quizResult->quiz_with_trash->title ?? '-'; //ready
//            $quizDate = $quizResult->created_at->format('d.m.Y H:i');//ready

//            $resultQuestions = collect($quizResult->questions)->keyBy('id')->toArray();
            //$resultQuestions это quiz results questions array и ключи массива это значение id внутри массива
            //questions row from quiz_results table
            //теперь нам нужны все вопросы квизов
            //чтобы из массива айдишников

            foreach ($quizResult->quiz_with_trash->questions as $question)
            {
                //у quiz_results есть quiz а у quiz есть quiz_questions
                //и мы находим ответ по answer id из quiz results
                //quiz_results table -> quiz table -> quiz_questions
                //и по айдигнику ответов мы находим ответ в quiz answers
                /** @var QuizAnswer $answer */
//                $answer = QuizAnswer::query()->find($resultQuestions[$question->id]['answer']); //ready
                $items [] = [
                    'id' => $quizResult->id, //ready
//                    'name' => $name, //ready
//                    'phone' => $phone, //ready
//                    'quiz' => $quiz, //ready
//                    'date' => $quizDate, //ready
//                    'bonus' => $quizResult->amount, //ready
//                    'question' => $question->question ?? '-', //ready
//                    'answer' => optional($answer)->getTranslation('answer', 'ru'), //ready
//                    'correct' => optional($answer)->correct ? 'Да' : 'Нет' //ready
                ];
            }
        }
        return collect($items);
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return ['ID', 'Ф.И.О.', 'Телефон', 'Викторина', 'Дата', 'Бонус', 'Вопрос', 'Ответ', 'Правильно'];
    }

}
