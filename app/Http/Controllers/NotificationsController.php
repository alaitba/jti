<?php

namespace App\Http\Controllers;

use App\Exports\SubscribedPartners;
use App\Http\Requests\AdminNotificationRequest;
use App\Http\Utils\ResponseBuilder;
use App\Models\AdminNotification;
use App\Models\Partner;
use App\Notifications\NotificationFromAdmin;
use App\Ui\Attributes\Align;
use App\Ui\Attributes\LineAwesomeIcon;
use App\Ui\Attributes\Modal;
use App\Ui\Components\Portlet\Portlet;
use App\Ui\Components\Table\AjaxLoadableTable;
use App\Ui\LayoutBuilder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Throwable;

class NotificationsController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index()
    {
        $portlet = new Portlet('Уведомления для пользователей ВП', LineAwesomeIcon::BELL);
        $portlet->addModalableIconButton(Modal::REGULAR, route('admin.notifications.create'), LineAwesomeIcon::PLUS, 'Отправить уведомление');
        $portlet->addUrlableIconButoon(route('admin.notifications.users'), LineAwesomeIcon::DOWNLOAD, 'Скачать список пользователей');
        $table = new AjaxLoadableTable(route('admin.notifications.list'), 'notificationsTable');
        $table->addColumn('#', Align::CENTER, '50');
        $table->addColumn('Админ', Align::LEFT);
        $table->addColumn('Тип', Align::LEFT);
        $table->addColumn('Заголовок', Align::LEFT);
        $table->addColumn('Текст', Align::LEFT);
        $table->addColumn('Файл', Align::LEFT);
        $table->addColumn('Дата', Align::LEFT);

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
        $items = AdminNotification::with('admin')->orderBy('created_at', 'desc')->paginate(30);

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('notifications.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#notificationsTable', $itemsHtml, $pages);
        return $response->makeJson();
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
                        'title' => 'Отправка уведомления',
                        'content' => view('notifications.form', [
                            'formAction' => route('admin.notifications.store'),
                            'buttonText' => 'Отправить',
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param AdminNotificationRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(AdminNotificationRequest $request)
    {
        $adminNotification = new AdminNotification($request->only(['type', 'title', 'message']));
        $adminNotification->admin_id = auth('admins')->id();
        $adminNotification->save();
        Notification::send(
            Partner::withoutTrashed()->whereNotNull('onesignal_token')->get(),
            new NotificationFromAdmin($adminNotification)
        );

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
                        'content' => view('notifications.table_row', ['item' => $adminNotification])->render()
                    ]
                ]
            ]
        ]);
    }

    public function getUsers()
    {
        return (new SubscribedPartners())->download('SubscribedUsers-' . now()->format('Y-m-d') . '.xlsx');
    }
}
