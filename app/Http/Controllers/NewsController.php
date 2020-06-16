<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use App\Http\Utils\ResponseBuilder;
use App\Imports\NewsPartnersImport;
use App\Jobs\NewsUsersImported;
use App\Models\News;
use App\Services\LogService\LogService;
use App\Services\MediaService\MediaService;
use App\Ui\Attributes\Modal;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

/**
 * Class NewsController
 * @package App\Http\Controllers
 */
class NewsController extends Controller
{
    private $mediaService;

    /**
     * NewsController constructor.
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
        return view('news.index');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = News::withoutTrashed()->paginate(25);

        return response()->json([
            'functions' => [
                'updateTableContent' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('news.list', [
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
                        'modal' => 'superLargeModal',
                        'title' => 'Добавление новости',
                        'content' => view('news.form', [
                            'formAction' => route('admin.news.store'),
                            'date' => new News(['from_date' => now()->startOfMonth(), 'to_date' => now()->endOfMonth()]),
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
       // dd($request->all());
        DB::beginTransaction();
        try {
            $news = News::query()->create($request->only(['title', 'contents', 'public', 'from_date','to_date']));

            if (!$news->public) {
                $file = $request->file('user_list');
                $fileName = $file->storeAs('newsusers', $news->id . '.' . $file->guessClientExtension());
                $news->user_list_file = $fileName;
                $news->save();
                (new NewsPartnersImport($news))->queue($fileName)->chain([
                    new NewsUsersImported($news->id)
                ]);
            }

            if ($request->has('image')) {
                $file = $request->file('image');
                $this->mediaService->upload($file, News::class, $news->id);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            LogService::logException($e);
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Не удалось создать новость.');
            return $response->makeJson();
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

        if (!$item->public && !$item->user_list_imported) {
            $item->setAttribute('disabled', 'Импорт списка пользователей еще не завершен');
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
                            'date' => new News(['from_date' => now()->startOfMonth(), 'to_date' => now()->endOfMonth()]),
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
        $params = $request->only(['title', 'contents', 'public', 'from_date','to_date']);
        $news->update($params);

        if (!$news->public && $request->has('user_list')) {
            $file = $request->file('user_list');
            $fileName = $file->storeAs('quizusers', $news->id . '.' . $file->guessClientExtension());
            $news->user_list_file = $fileName;
            $news->save();
            (new NewsPartnersImport($news))->queue($fileName)->chain([
                new NewsUsersImported($news->id)
            ]);
        }

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

    /**
     * @param $id
     * @return mixed
     */
    public function getFile($id)
    {
        $news = News::query()->find($id);
        if (!$news || $news->public)
        {
            abort(404);
        }
        return Storage::disk('local')
            ->download(
                $news->user_list_file,
                'NewsPartners-' . $id . '.' . pathinfo($news->user_list_file, PATHINFO_EXTENSION)
            );
    }
}
