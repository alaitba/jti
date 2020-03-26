<?php

namespace App\Exports;

use App\Models\PollResult;
use App\Models\QuizAnswer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PollResults implements FromCollection, ShouldAutoSize, WithHeadings
{
    private $pollResults;

    public function __construct($pollResults)
    {
        $this->pollResults = $pollResults;
    }

    /**
    * @return Collection
    */
    public function collection()
    {
        $items = [];
        /** @var PollResult $pollResult */
        foreach ($this->pollResults as $pollResult)
        {
            $name = $pollResult->partner->current_contact->name ?? '-';
            $phone = $pollResult->partner->mobile_phone ?? '-';
            $poll = $pollResult->quiz->title ?? '-';
            $pollDate = $pollResult->created_at->format('d.m.Y H:i');

            $resultQuestions = collect($pollResult->questions)->keyBy('id')->toArray();

            foreach ($pollResult->quiz->questions as $question)
            {
                if ($question->type == 'text')
                {
                    $answer = $resultQuestions[$question->id]['answer'] ?? '';
                } else {
                    $ids = is_array($resultQuestions[$question->id]['answer'])
                        ? $resultQuestions[$question->id]['answer']
                        : explode(',', $resultQuestions[$question->id]['answer']);
                    $answers = QuizAnswer::query()
                        ->whereIn('id', $ids)
                        ->get('answer');
                    if (count($answers) == 1)
                    {
                        $answer = $answers[0]->getTranslation('answer', 'ru');
                    } else {
                        $answersArray = [];
                        foreach ($answers as $ans)
                        {
                            $answersArray [] = '•' . $ans->getTranslation('answer', 'ru');
                        }
                        $answer = implode('; ', $answersArray);
                    }
                }
                $items []= [
                    'id' => $pollResult->id,
                    'name' => $name,
                    'phone' => $phone,
                    'poll' => $poll,
                    'date' => $pollDate,
                    'bonus' => $pollResult->amount,
                    'question' => $question->question ?? '-',
                    'answer' => $answer
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
        return ['ID', 'Ф.И.О.', 'Телефон', 'Опрос', 'Дата', 'Бонус', 'Вопрос', 'Ответ'];
    }

}
