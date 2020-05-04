<?php

namespace App\Http\Controllers;


use App\Http\Requests\QuizQuestionRequest;
use App\Http\Requests\QuizRequest;
use App\Http\Utils\ResponseBuilder;
use App\Imports\QuizPartnersImport;
use App\Jobs\QuizUsersImported;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Services\LogService\LogService;
use App\Services\MediaService\MediaService;
use App\Ui\Attributes\Align;
use App\Ui\Attributes\LineAwesomeIcon;
use App\Ui\Attributes\Modal;
use App\Ui\Components\Portlet\Portlet;
use App\Ui\Components\Table\AjaxLoadableTable;
use App\Ui\LayoutBuilder;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
                (new QuizPartnersImport($quiz))->queue($fileName)->chain([
                    new QuizUsersImported($quiz->id)
                ]);
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
        $item = Quiz::with('photo')->withCount('questions')->find($quizId);
        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Викторина не найдена');
            $response->closeModal(Modal::REGULAR);
            return $response->makeJson();
        }

        if ($item->questions_count == 0)
        {
            $item->setAttribute('disabled', 'У ' . ($item->type == 'quiz' ? 'викторины' : 'опроса') . ' нет вопросов');
        } elseif (!$item->public && !$item->user_list_imported) {
            $item->setAttribute('disabled', 'Импорт списка пользователей еще не завершен');
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Редактирование викторины',
                        'init' => 'bootstrap_select',
                        'content' => view('quiz.edit_form', [
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
     * @param QuizRequest $request
     * @param $quizId
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(QuizRequest $request, $quizId)
    {
        /** @var Quiz $quiz */
        $quiz = Quiz::query()->find($quizId);
        $params = $request->only(['title', 'from_date', 'to_date', 'amount', 'public']);
        $params['active'] = (int) $request->input('active', 0);
        $quiz->update($params);
        if (!$quiz->public) {
            $file = $request->file('user_list');
            $fileName = $file->storeAs('quizusers', $quiz->id . '.' . $file->guessClientExtension());
            $quiz->user_list_file = $fileName;
            $quiz->save();
            (new QuizPartnersImport($quiz))->queue($fileName)->chain([
                new QuizUsersImported($quiz->id)
            ]);
        }
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $this->mediaService->upload($file, Quiz::class, $quiz->id);
        }

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                    ]
                ],
                'updateTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $quizId,
                        'content' => view('quiz.item',['item' => $quiz])->render()
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
        $item = Quiz::query()->find($id);

        if($item) {
            $this->mediaService->deleteForModel(Quiz::class, $id);
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

    /**
     * @param $quizId
     * @return Factory|View
     * @throws Throwable
     */
    public function questions($quizId)
    {
        $quiz = Quiz::withoutTrashed()->with('questions')->findOrFail($quizId);
        $portlet = new Portlet('Вопросы к ' . ($quiz->type == 'quiz' ? 'викторине' : 'опросу') . ' "' . $quiz->title . '"', LineAwesomeIcon::QUESTION_CIRCLE);
        $portlet->addUrlableIconButoon(route('admin.quizzes.index'), LineAwesomeIcon::CIRCLE_LEFT, 'Назад к викторинам');
        $portlet->addModalableIconButton(Modal::LARGE, route('admin.quizzes.questions.create', ['quizId' => $quizId]), LineAwesomeIcon::PLUS, 'Добавить вопрос');

        $table = new AjaxLoadableTable(route('admin.quizzes.questions.list', ['quizId' => $quizId]), 'questionsTable');
        $table->addColumn('Вопрос', Align::LEFT);
        $table->addColumn('Тип', Align::CENTER, 150);
        $table->addColumn(LineAwesomeIcon::EDIT, Align::CENTER, 50);
        $table->addColumn(LineAwesomeIcon::TRASH, Align::CENTER, 50);

        $portlet->setContent([$table]);

        $layout = new LayoutBuilder();
        $layout->addRow([$portlet]);

        return $layout->build();
    }

    /**
     * @param Request $request
     * @param $quizId
     * @return JsonResponse
     * @throws Throwable
     */
    public function questionsList($quizId)
    {
        $quiz = Quiz::withoutTrashed()->with('questions')->findOrFail($quizId);
        $items = $quiz->questions()->paginate(25);

        $itemsHtml = '';
        foreach ($items as $item)
        {
            $itemsHtml .= view('quiz.questions.item', ['question' => $item, 'quizId' => $quizId])->toHtml();
        }

        $response = new ResponseBuilder();
        $response->closeModal('largeModal');
        $response->updateTableContentHtml('#questionsTable', $itemsHtml, $items->appends(request()->all())->links('pagination.bootstrap-4'));

        return $response->makeJson();

    }

    /**
     * @param $quizId
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function deleteQuestion($quizId, $id)
    {
        QuizQuestion::query()->where('id', $id)->delete();
        $this->mediaService->deleteForModel(QuizQuestion::class, $id);
        return $this->questionsList($quizId);
    }

    /**
     * @param $quizId
     * @return JsonResponse
     * @throws Throwable
     */
    public function createQuestion($quizId)
    {
        $quiz = Quiz::withoutTrashed()->findOrFail($quizId);
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Добавление вопроса',
                        'content' => view('quiz.questions.form', [
                            'formAction' => route('admin.quizzes.questions.store', ['quizId' => $quizId]),
                            'quizType' => $quiz->type
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param QuizQuestionRequest $request
     * @param $quizId
     * @return JsonResponse
     * @throws Throwable
     */
    public function storeQuestion(QuizQuestionRequest $request, $quizId)
    {
        $quizQuestion = new QuizQuestion(['quiz_id' => $quizId]);
        $quizQuestion->fill($request->only(['question', 'type']));
        $quizQuestion->save();
        if ($request->has('photo')) {
            $file = $request->file('photo');
            $this->mediaService->upload($file, QuizQuestion::class, $quizQuestion->id);
        }

        if ($request->input('type') == 'choice')
        {
            $answers = [];
            $correct = $request->input('new-correct');
            foreach ($request->input('new-answer', []) as $idx => $answer)
            {
                $answers []= new QuizAnswer([
                    'quiz_question_id' => $quizQuestion->id,
                    'answer' => $answer,
                    'correct' => $correct[$idx] ?? false
                ]);
            }
            $quizQuestion->answers()->saveMany($answers);

            for ($i = 0; $i < count($answers); $i++) {
                if (isset($request['new-answer'][$i]['file'])) {
                    $this->mediaService->upload($request['new-answer'][$i]['file'], QuizAnswer::class, $answers[$i]->id);
                }
            }
        }
        return $this->questionsList($quizId);
    }

    /**
     * @param $quizId
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function editQuestion($quizId, $id)
    {
        $quiz = Quiz::withoutTrashed()->findOrFail($quizId);
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Редактирование вопроса',
                        'content' => view('quiz.questions.form', [
                            'formAction' => route('admin.quizzes.questions.update', ['quizId' => $quizId, 'id' => $id]),
                            'quizType' => $quiz->type,
                            'question' => QuizQuestion::query()->find($id)
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param QuizQuestionRequest $request
     * @param $quizId
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function updateQuestion(QuizQuestionRequest $request, $quizId, $id)
    {
        $quizQuestion = QuizQuestion::query()->find($id);
        $quizQuestion->fill($request->only(['question', 'type']));
        $quizQuestion->save();
        if ($request->has('photo')) {
            $this->mediaService->deleteForModel(QuizQuestion::class, $id);
            $file = $request->file('photo');
            $this->mediaService->upload($file, QuizQuestion::class, $id);
        }

        if ($request->input('type') == 'choice')
        {
            $ids = [];
            $correct = $request->input('correct');

            foreach ($request->input('answer', []) as $answerId => $answer)
            {
                $ids []= $answerId;
                $quizAnswer = QuizAnswer::query()->find($answerId);
                $quizAnswer->update([
                    'answer' => $answer,
                    'correct' => $correct[$answerId] ?? false
                ]);
            }
            QuizAnswer::query()->whereNotIn('id', $ids)->where('quiz_question_id', $id)->delete();
            $answers = [];
            $correct = $request->input('new-correct');
            for ($i = 0; $i < count($ids); $i++) {
                if (isset($request['answer'][$ids[$i]]['file'])) {
                    $this->mediaService->deleteForModel(QuizAnswer::class, $ids[$i]);
                    $this->mediaService->upload($request['answer'][$ids[$i]]['file'], QuizAnswer::class, $ids[$i]);
                }
            }
            if ($request->input('new-answer')) {
                foreach ($request->input('new-answer', []) as $idx => $answer)
                {
                    $answers []= new QuizAnswer([
                        'quiz_question_id' => $quizQuestion->id,
                        'answer' => $answer,
                        'correct' => $correct[$idx] ?? false
                    ]);
                }
                $quizQuestion->answers()->saveMany($answers);
                for ($i = 0; $i < count($answers); $i++) {
                    if (isset($request['new-answer'][$i]['file'])) {

                        $this->mediaService->upload($request['new-answer'][$i]['file'], QuizAnswer::class, $answers[$i]->id);
                    }
                }
            }
        } else {
            QuizAnswer::query()->where('quiz_question_id', $id)->delete();
        }
        return $this->questionsList($quizId);
    }

    /**
     * @param $id
     */
    public function copy($id)
    {
        $item = Quiz::withoutTrashed()->findOrFail($id);
        $clone = $item->replicate();
        $clone->push();
        $clone->photo()->save($item->photo->replicate());
        foreach($item->questions as $question)
        {
            $clone->questions()->save($question->replicate());
            if (isset($item->questions->photo)){
                $clone->questions->first()->photo()->save($question->photo->replicate());
            }
        }
        $answersModel = $item->questions->first()->answers;
        foreach($answersModel as $answer)
        {
            $clone->questions->first()->answers()->save($answer->replicate());
        }
        for ($i = 0; $i < count($answersModel); $i++) {
            if (isset($answersModel[$i]->photo)) {
                $clone->questions->first()->answers[$i]->photo()->save($answersModel[$i]->photo->replicate());
            }
        }
    }
}
