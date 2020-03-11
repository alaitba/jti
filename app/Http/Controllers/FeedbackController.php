<?php

namespace App\Http\Controllers;


use App\Http\Utils\ResponseBuilder;
use App\Models\Feedback;
use App\Models\FeedbackTopic;
use App\Notifications\FeedbackPartnerNotification;
use App\Ui\Attributes\Align;
use App\Ui\Attributes\LineAwesomeIcon;
use App\Ui\Attributes\Modal;
use App\Ui\Components\Form\Factory;
use App\Ui\Components\Portlet\Portlet;
use App\Ui\Components\Table\AjaxLoadableTable;
use App\Ui\LayoutBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Throwable;

/**
 * Class FeedbackController
 * @package App\Http\Controllers
 */
class FeedbackController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index()
    {
        $portlet = new Portlet('Обратная связь', LineAwesomeIcon::COMMENT);
        $portlet->addUrlableIconButoon(route('admin.feedback.topics'), LineAwesomeIcon::COMMENTING, 'Темы вопросов');

        $table = new AjaxLoadableTable(route('admin.feedback.list'), 'feedbackTable');
        $table->addColumn('#', Align::CENTER, '50');
        $table->addColumn('Телефон', Align::LEFT);
        $table->addColumn('Тема', Align::LEFT);
        $table->addColumn('Вопрос', Align::LEFT);
        $table->addColumn('Ответ', Align::LEFT);
        $table->addColumn('Дата', Align::LEFT);
        $table->addColumn(LineAwesomeIcon::EDIT, Align::CENTER, 50);

        $portlet->setContent([$table]);

        $layout = new LayoutBuilder();
        $layout->addRow([$portlet]);

        return $layout->build();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = Feedback::query()->paginate(30);

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('feedback.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');

        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#feedbackTable', $itemsHtml, $pages);

        return $response->makeJson();
    }


    /**
     * @param $feedbackId
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($feedbackId)
    {
        $item = Feedback::with('topic')->find($feedbackId);

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Вопрос не найден');
            $response->closeModal(Modal::LARGE);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Вопрос №' . $feedbackId,
                        'content' => view('feedback.form', [
                            'formAction' => route('admin.feedback.update', ['id' => $feedbackId]),
                            'buttonText' => 'Ответить',
                            'item' => $item,
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param $feedbackId
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(Request $request, $feedbackId)
    {
        $item = Feedback::query()->findOrFail($feedbackId);
        $item->fill(['answer' => $request->input('answer')]);
        $item->save();

        Notification::send($item->partner, new FeedbackPartnerNotification());

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                    ]
                ],
                'updateTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $feedbackId,
                        'content' => view('feedback.table_row', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function indexTopics()
    {
        $portlet = new Portlet('Темы вопросов', LineAwesomeIcon::COMMENTING);
        $portlet->addUrlableIconButoon(route('admin.feedback.index'), LineAwesomeIcon::CIRCLE_LEFT, 'Назад к вопросам');
        $portlet->addModalableIconButton(Modal::REGULAR, route('admin.feedback.topics.create'), LineAwesomeIcon::PLUS, 'Создать тему');

        $table = new AjaxLoadableTable(route('admin.feedback.topics.list'), 'topicsTable');
        $table->addColumn('#', Align::CENTER, '50');
        $table->addColumn('Заголовок', Align::LEFT);
        $table->addColumn(LineAwesomeIcon::EDIT, Align::CENTER, 50);
        $table->addColumn(LineAwesomeIcon::TRASH, Align::CENTER, 50);

        $portlet->setContent([$table]);

        $layout = new LayoutBuilder();
        $layout->addRow([$portlet]);

        return $layout->build();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function getTopicsList(Request $request)
    {
        $items = FeedbackTopic::withoutTrashed()->get();
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('feedback.topic_table_row', ['item' => $item])->render();
        }

        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#topicsTable', $itemsHtml, '');

        return $response->makeJson();

    }

    /**
     * @return JsonResponse
     * @throws Throwable
     */
    public function createTopic()
    {
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Создание темы вопросов',
                        'content' => view('feedback.topic_form', [
                            'formAction' => route('admin.feedback.topics.store'),
                            'buttonText' => 'Создать',
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function storeTopic(Request $request)
    {
        $item = new FeedbackTopic(['title' => $request->get('title')]);
        $item->save();

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
                        'content' => view('feedback.topic_table_row', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param $topicId
     * @return JsonResponse
     * @throws Throwable
     */
    public function editTopic($topicId)
    {
        $item = FeedbackTopic::withoutTrashed()->find($topicId);

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Тема не найдена');
            $response->closeModal(Modal::REGULAR);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Редактирование темы',
                        'content' => view('feedback.topic_form', [
                            'formAction' => route('admin.feedback.topics.update', ['topicId' => $topicId]),
                            'buttonText' => 'Сохранить',
                            'item' => $item,
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }


    /**
     * @param Request $request
     * @param $topicId
     * @return JsonResponse
     * @throws Throwable
     * @author Rishat Sultanov
     */
    public function updateTopic(Request $request, $topicId)
    {
        $item = FeedbackTopic::query()->find($topicId);
        $item->fill($request->only('title'));
        $item->save();

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
                        'row' => '.row-' . $topicId,
                        'content' => view('feedback.topic_table_row', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param int $topicId
     * @return JsonResponse
     */
    public function deleteTopic(int $topicId)
    {
        FeedbackTopic::query()->where('id', $topicId)->delete();
        return response()->json([
            'functions' => [
                'deleteTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $topicId
                    ]
                ]
            ]
        ]);
    }
}
