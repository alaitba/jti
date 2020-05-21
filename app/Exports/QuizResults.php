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
            $name = $quizResult->partner->current_contact->name ?? '-';
            $phone = $quizResult->partner->mobile_phone ?? '-';
            $quiz = $quizResult->quiz_with_trash->title ?? '-';
            $quizDate = $quizResult->created_at->format('d.m.Y H:i');

            $resultQuestions = collect($quizResult->questions)->keyBy('id')->toArray();

            foreach ($quizResult->quiz_with_trash->questions as $question)
            {
                /** @var QuizAnswer $answer */
                $answer = QuizAnswer::query()->find($resultQuestions[$question->id]['answer']);
                $items [] = [
                    'id' => $quizResult->id,
                    'name' => $name,
                    'phone' => $phone,
                    'quiz' => $quiz,
                    'date' => $quizDate,
                    'bonus' => $quizResult->amount,
                    'question' => $question->question ?? '-',
                    'answer' => optional($answer)->getTranslation('answer', 'ru'),
                    'correct' => optional($answer)->correct ? 'Да' : 'Нет'
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
