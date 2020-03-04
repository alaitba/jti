<?php

namespace App\Http\Controllers;


use App\Models\Quiz;
use App\Services\MediaService\MediaService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Class QuizController
 * @package App\Http\Controllers
 */
class QuizController extends Controller
{
    private $mediaService;

    /**
     * QuizController constructor.
     */
    public function __construct()
    {
        $this->mediaService = new MediaService();
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('quiz.index');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = Quiz::query()->paginate(25);

        return response()->json([
            'functions' => [
                'updateTableContent' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('quiz.list', [
                            'items' => $items,
                        ])->render(),
                        'pagination' => view('layouts.pagination', [
                            'links' => $items->appends($request->all())->links('pagination.bootstrap-4'),
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }


    /**
     * @return JsonResponse
     * @throws Throwable
     */
    public function create()
    {
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Добавление викторины',
                        'content' => view('quiz.form', [
                            'formAction' => route('admin.quizzes.store'),
                            'buttonText' => 'Сохранить',
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param NewsRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(NewsRequest $request)
    {
        $news = News::query()->create($request->only(['title', 'contents']));

        if ($request->has('image')) {
            $file = $request->file('image');
            $this->mediaService->upload($file, News::class, $news->id);
        }

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'superLargeModal',
                    ]
                ],
                'prependTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('news.item', ['item' => $news])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param $newsId
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($newsId)
    {
        $item = News::with('media')->find($newsId);
        $medias = $item->media->chunk(2);

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Новость не найдена');
            $response->closeModal(Modal::SUPER_LARGE);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'superLargeModal',
                        'title' => 'Редактирование новости',
                        'content' => view('news.form', [
                            'formAction' => route('admin.news.update', $newsId),
                            'buttonText' => 'Сохранить',
                            'medias' => $medias,
                            'item' => $item,
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param NewsRequest $request
     * @param $newsId
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(NewsRequest $request, $newsId)
    {
        $news = News::query()->find($newsId);
        $news->update($request->all());

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'superLargeModal',
                    ]
                ],
                'updateTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $newsId,
                        'content' => view('news.item',['item' => $news])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function delete($id)
    {
        $item = News::query()->find($id);

        if($item) {
            $this->mediaService->deleteForModel(News::class, $id);
            $item->delete();
        }

        return response()->json([
            'functions' => [
                'deleteTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-'.$id
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param int $itemId
     * @return JsonResponse
     * @throws Throwable
     */
    public function media(Request $request, int $itemId)
    {
        foreach ($request->file('image') as $image) {
            $this->mediaService->upload($image, News::class, $itemId);
        }

        $items = News::query()->find($itemId);

        return response()->json(([
            'media' => view('news.media_list', ['items' => $items])->render(), // DO NOT FORGET MAYBE WRONG
        ]));
    }

    /**
     * @param $mediaId
     * @throws Exception
     */
    public function deleteMedia($mediaId)
    {
        $this->mediaService->deleteById($mediaId);
    }
}
