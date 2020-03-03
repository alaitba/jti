<?php

namespace App\Http\Controllers;

use App\Exports\SubscribedPartners;
use App\Http\Requests\AdminNotificationRequest;
use App\Http\Utils\ResponseBuilder;
use App\Imports\CustomSubscribers;
use App\Models\AdminNotification;
use App\Models\Partner;
use App\Notifications\NotificationFromAdmin;
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
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Class NotificationsController
 * @package App\Http\Controllers
 */
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
        DB::beginTransaction();
        try {
            $adminNotification = new AdminNotification($request->only(['type', 'title', 'message']));
            $adminNotification->admin_id = auth('admins')->id();
            $adminNotification->save();
            if ($adminNotification->type == 'list') {
                $file = $request->file('user_list');
                $fileName = $file->store('subscribers');
                $adminNotification->user_list_file = $fileName;
                $adminNotification->save();
                Excel::import(new CustomSubscribers($adminNotification), $fileName);
            } else {
                Notification::send(
                    Partner::withoutTrashed()->whereNotNull('onesignal_token')->get(),
                    new NotificationFromAdmin($adminNotification)
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Не удалось загрузить файл.');
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
                        'content' => view('notifications.table_row', ['item' => $adminNotification])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @return BinaryFileResponse
     */
    public function getUsers()
    {
        return (new SubscribedPartners())->download('SubscribedUsers-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getFile($id)
    {
        $notification = AdminNotification::query()->find($id);
        if (!$notification || $notification->type == 'all')
        {
            abort(404);
        }
        return Storage::disk('local')
            ->download(
                $notification->user_list_file,
                'Subscribers-' . $id . '.' . pathinfo($notification->user_list_file, PATHINFO_EXTENSION)
            );
    }
}
