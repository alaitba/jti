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
     * @param int|null $id
     * @return JsonResponse
     */
    public function getList($id = null)
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
            }); //TODO whereNotHas заполненная викторина
        if ($id)
        {
            $quizzes = $quizzes->where('id', $id);
        }

        $quizzes = $quizzes->get();


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
        if ($id)
        {
            return response()->json([
                'status' => 'ok',
                'quiz' => array_pop($items)
            ]);
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
        /** @var Partner $user */
        $user = auth('partners')->user();

        $quiz = $request->input('quiz', []);
        if (!$quiz['id'])
        {
            return response()->json([
                'status' => 'error',
                'message' => 'no_quiz_id'
            ], 422);
        }
        $quizDB = Quiz::withoutTrashed()->find($quiz['id']);
        if (!$quizDB)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'quiz_not_found'
            ], 404);
        }
        if ($quizDB->type == 'quiz' && $quizDB->hasSuccess($user))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'have_success'
            ], 403);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

}
