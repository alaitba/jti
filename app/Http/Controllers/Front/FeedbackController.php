<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackTopic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class FeedbackController
 * @package App\Http\Controllers\Front
 */
class FeedbackController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getTopics()
    {
        return response()->json([
            'status' => 'ok',
            'data' => FeedbackTopic::withoutTrashed()->get(['id', 'title'])->toArray()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFeedbacks(Request $request)
    {
        $items = Feedback::with(['topic_all' => function(BelongsTo $q) {
            $q->select('id', 'title');
        }])->where('partner_id', auth('partners')->id())
            ->where('created_at', '>=', $request->input('from_date', Carbon::now()->subYear()))
            ->orderBy('created_at', 'DESC')->get(['feedback_topic_id', 'question', 'answer', 'created_at']);
        return response()->json([
            'status' => 'ok',
            'data' => $items->toArray()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function question(Request $request)
    {
        $params = $request->only(['feedback_topic_id', 'question']);
        $params['partner_id'] = auth('partners')->id();
        Feedback::query()->create($params);
        return response()->json([
            'status' => 'ok',
        ]);
    }
}
