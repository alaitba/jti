<?php

namespace App\Http\Controllers\Front;



use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PollResult;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use Exception;
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
            });
        if ($id)
        {
            $quizzes = $quizzes->where('id', $id);
        }

        $quizzes = $quizzes->get();


        $items = [];
        foreach ($quizzes as $quiz)
        {
            if ($quiz->hasSuccess($user))
            {
                continue;
            }
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
        if (!isset($quiz['id']))
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
        if ($quizDB->hasSuccess($user))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'have_success'
            ], 403);
        }

        $results = [];

        if ($quizDB->type == 'poll')
        {
            $quizResult = PollResult::query()->create([
                'quiz_id' => $quizDB->id,
                'partner_id' => $user->id,
                'amount' => $quizDB->amount,
                'questions' => $quiz['questions']
            ]);
            $results['correct'] = $results['total'] = $quizDB->questions()->count();
            $success = true;
        } else {
            $quizResult = new QuizResult([
                'quiz_id' => $quizDB->id,
                'partner_id' => $user->id,
                'amount' => $quizDB->amount,
                'questions' => $quiz['questions']
            ]);
            $results = $this->checkQuizAnswers($quiz, $quizDB);
            $success = $results['correct'] == $results['total'];
            $quizResult->success = $success;
            $quizResult->save();
        }

        $moneyStatus = 'no';
        if ($quizDB->amount > 0 && $success)
        {
            try {
                $result = JtiApiProvider::createMoneyReward(auth('partners')->user()->current_uid, $quizDB->amount, $quizResult->id)->getBody();
                $result = json_decode($result, true);
                $moneyStatus = $result['result'] ? 'ok' : 'failed';
            } catch (Exception $e) {
                $moneyStatus = 'failed';
                LogService::logInfo('Seller: ' . auth('partners')->user()->current_uid);
                LogService::logException($e);
            }
        }
        return response()->json([
            'status' => 'ok',
            'correct' => $results['correct'],
            'total' => $results['total'],
            'success' => $success,
            'amount' => $quizDB->amount,
            'money_status' => $moneyStatus
        ]);
    }

    /**
     * @param array $quiz
     * @param Quiz $quizDB
     * @return array
     */
    private function checkQuizAnswers($quiz, $quizDB)
    {
        $questions = collect($quiz['questions'])->keyBy('id')->toArray();
        $correct = 0;
        /** @var QuizQuestion $question */
        foreach ($quizDB->questions as $question)
        {
            $correctAnswerIds = $question->answers()->where('correct', 1)->get('id')->pluck('id')->toArray();
            $answer = $questions[$question->id]['answer'] ?? null;
            if (in_array($answer, $correctAnswerIds))
            {
                $correct++;
            }
        }
        return ['correct' => $correct, 'total' => count($quizDB->questions)];
    }

    public function getHistory()
    {
        /** @var Partner $user */
        $user = auth('partners')->user();

        $quizzes = QuizResult::query()->where('partner_id', $user->id)->where('success',1)->get()
            ->merge(PollResult::query()->where('partner_id', $user->id)->get());

        $items = [];
        /** @var QuizResult|PollResult $quizResult */
        foreach($quizzes as $quizResult)
        {
            $items []= [
                'type' => $quizResult->quiz->type,
                'title' => $quizResult->quiz->getTranslations('title') ?? '',
                'completed_at' => $quizResult->created_at->toDateTimeString(),
                'amount' => $quizResult->amount,
                'photo' => $quizResult->quiz->photo->url ?? null
            ];
        }
        return response()->json([
            'status' => 'ok',
            'quizzes' => $items
        ]);
    }
}
