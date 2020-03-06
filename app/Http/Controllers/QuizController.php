<?php

namespace App\Http\Controllers;


use App\Http\Requests\QuizRequest;
use App\Http\Utils\ResponseBuilder;
use App\Imports\QuizPartnersImport;
use App\Models\Quiz;
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
use Maatwebsite\Excel\Facades\Excel;
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
        $items = Quiz::query()->withCount('partners')->paginate(25);

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
                            'item' => new Quiz(['from_date' => now()->startOfMonth(), 'to_date' => now()->endOfMonth()])
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param QuizRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(QuizRequest $request)
    {
        DB::beginTransaction();
        try {
            /** @var Quiz $quiz */
            $quiz = Quiz::query()->create($request->only(['type', 'public', 'title', 'from_date', 'to_date', 'amount']));
            if (!$quiz->public) {
                $file = $request->file('user_list');
                $fileName = $file->storeAs('quizusers', $quiz->id . '.' . $file->guessClientExtension());
                $quiz->user_list_file = $fileName;
                $quiz->save();
                Excel::queueImport(new QuizPartnersImport($quiz),  $fileName, 'local');
            }
            if ($request->has('photo')) {
                $file = $request->file('photo');
                $this->mediaService->upload($file, Quiz::class, $quiz->id);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            LogService::logException($e);
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Не удалось создать викторину.');
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                    ]
                ],
                'prependTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('quiz.item', ['item' => $quiz])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param $quizId
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($quizId)
    {
        $item = Quiz::with('photo')->find($quizId);
        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Викторина не найдена');
            $response->closeModal(Modal::REGULAR);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Редактирование викторины',
                        'init' => 'bootstrap_select',
                        'content' => view('quiz.form', [
                            'formAction' => route('admin.quizzes.update', $quizId),
                            'buttonText' => 'Сохранить',
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


    /**
     * @param $id
     * @return mixed
     */
    public function getFile($id)
    {
        $quiz = Quiz::query()->find($id);
        if (!$quiz || $quiz->public)
        {
            abort(404);
        }
        return Storage::disk('local')
            ->download(
                $quiz->user_list_file,
                'QuizPartners-' . $id . '.' . pathinfo($quiz->user_list_file, PATHINFO_EXTENSION)
            );
    }
}
