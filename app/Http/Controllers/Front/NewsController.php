<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\News;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class NewsController
 * @package App\Http\Controllers\Front
 */
class NewsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getNews(Request $request)
    {
        /** @var Partner $user */
        $user = auth('partners')->user();

        $today = now();
        $news = News::withoutTrashed()->with(['media' => function(MorphMany $q) {
            $q->select('id', 'imageable_id', 'imageable_type', 'original_file_name', 'conversions', 'mime');
        }])->where('created_at', '>', $request->input('from_date') ?? '1970-01-01 00:00:00')
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->where(function (Builder $q) use ($user) {
                $q->where('public', 1)->orWhereHas('partners', function (Builder $qq) use ($user) {
                    $qq->where('partner_id', $user->id);
                });
            });

        if ($perPage = $request->input('perpage', 0))
        {
            $news = $news->skip(($request->input('page', 0) - 1) * $perPage)->take($perPage);
        } else {
            $news = $news->limit(10);
        }
        $news = $news->orderBy('id', 'DESC')->get(['id', 'title', 'contents', 'created_at']);

        $newsItems = $news->keyBy('id')->map(function (News $newsItem) {
            $newsItem->media->map(function (Media $media) {
                $media->makeHidden([
                    'id',
                    'imageable_id',
                    'imageable_type',
                    'original_file_name',
                    'url_reverse_proxy'
                ]);
                $conversions = collect($media->conversions)->map(function ($conversion) {
                    return collect($conversion)->only(['mime', 'width', 'height', 'url']);
                });
                $media->setAttribute('sizes', $conversions);
                $media->makeHidden('conversions');
                return $media;
            });
            return $newsItem;
        })->toArray();

        return response()->json([
            'status' => 'ok',
            'data' => $newsItems
        ]);
    }
}
