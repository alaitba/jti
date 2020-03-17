<?php

namespace App\Http\Controllers\Front;



use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class QuizController
 * @package App\Http\Controllers\Front
 */
class QuizController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function getList()
    {
        /** @var Partner $user */
        $user = auth('partners')->user();

        $today = now();
        $quizzes = Quiz::withoutTrashed()->with('photo')
            ->where('active', 1)
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->where(function (Builder $q) use ($user) {
                $q->where('public', 1)->orWhereHas('partners', function (Builder $qq) use ($user) {
                    $qq->where('partner_id', $user->id);
                });
            }) //TODO whereNotHas заполненная викторина
            ->get();
        $items = [];
        foreach ($quizzes as $quiz)
        {
            $item = $quiz->only(['id', 'type', 'amount']);
            $item['title'] = $quiz->getTranslations('title');
            $item['photo'] = $quiz->photo->url ?? null;
            $item['period'] = $quiz->period;
            $item['questions'] = [];
            $questions = $quiz->questions()->with(['answers', 'photo'])->get();

            /** @var QuizQuestion $question */
            foreach ($questions as $question)
            {
                $itemQuestion = $question->only(['id', 'type']);
                $itemQuestion['question'] = $question->getTranslations('question');
                $itemQuestion['photo'] = $question->photo->url ?? null;
                if ($question->type == 'choice')
                {
                    $itemQuestion['answers'] = [];
                    /** @var QuizAnswer $answer */
                    foreach ($question->answers as $answer)
                    {
                        $itemQuestion['answers'] []= [
                            'id' => $answer->id,
                            'answer' => $answer->getTranslations('answer')
                        ];
                    }
                }
                $item['questions'] []= $itemQuestion;
            }

            $items []= $item;
        }
        return response()->json([
            'status' => 'ok',
            'quizzes' => $items
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkQuiz(Request $request)
    {
        return response()->json([
            'status' => 'ok',
        ]);
    }

}
