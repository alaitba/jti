<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Models\News;
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
        $items = [];
        $news = News::withoutTrashed()->where('created_at', '>', $request->input('from_date', '1970-01-01 00:00:00'))
            ->orderBy('id', 'DESC')->get(['id', 'title', 'contents', 'created_at']);

        return response()->json([
            'status' => 'ok',
            'data' => $news
        ]);
    }
}
