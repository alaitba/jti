<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\News;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getNews(Request $request)
    {
        $news = News::withoutTrashed()->with(['media' => function(MorphMany $q) {
            $q->select('id', 'imageable_id', 'imageable_type', 'original_file_name', 'conversions', 'mime');
        }])->where('created_at', '>', $request->input('from_date', '1970-01-01 00:00:00'));

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
                $conversions = $media->conversions;
                foreach ($conversions as $key => $conv)
                {
                    unset($conversions[$key]['name']);
                    unset($conversions[$key]['url_reverse_proxy']);
                }
                $media->setAttribute('sizes', $conversions);
                $media->makeHidden('conversions');
                return $media->toArray();
            });
            return $newsItem->toArray();
        })->toArray();

        return response()->json([
            'status' => 'ok',
            'data' => $newsItems
        ]);
    }
}
