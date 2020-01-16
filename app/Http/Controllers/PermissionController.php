<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PermissionUpdateRequest;
use App\Http\Utils\ResponseBuilder;
use App\Ui\Attributes\Align;
use App\Ui\Attributes\LineAwesomeIcon;
use App\Ui\Attributes\Modal;
use App\Ui\Components\Portlet\Portlet;
use App\Ui\Components\Table\AjaxLoadableTable;
use App\Ui\Components\Table\TableContent;
use App\Ui\LayoutBuilder;
use App\UseCases\PermissionsCase;
use Illuminate\View\View;
use App\Http\Requests\PermissionRequest;
use Throwable;

/**
 * Class PermissionController
 * @package App\Http\Controllers
 */
class PermissionController extends Controller
{
    private $permissionsCase;

    /**
     * PermissionController constructor.
     * @param PermissionsCase $permissionsCase
     */
    public function __construct(PermissionsCase $permissionsCase)
    {
        $this->permissionsCase = $permissionsCase;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $portlet = new Portlet('Права', LineAwesomeIcon::BAN);
        $portlet->addUrlableIconButoon(route('admin.admins'), LineAwesomeIcon::CIRCLE_LEFT, 'Назад к администраторам');
        $portlet->addModalableIconButton(Modal::LARGE, route('admin.admins.permissions.create'), LineAwesomeIcon::PLUS, 'Создать права');

        $table = new AjaxLoadableTable(route('admin.admins.permissions.list'), 'adminsPermissionsTable');
        $table->addColumn('#', Align::CENTER, '50');
        $table->addColumn('Название', Align::LEFT);
        $table->addColumn("Дата", Align::CENTER, 150);
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
    public function getList(Request $request)
    {
        $items = $this->permissionsCase
            ->where('guard_name', 'admin')
            ->getList();

        $tableContent = new TableContent($items);

        $tableContent->textColumn('id', Align::CENTER);
        $tableContent->textColumn('name');
        $tableContent->textColumn('created_at', Align::CENTER);
        $tableContent->linkColumn('id', 'admin.admins.permissions.edit', ['permissionId' => 'id'], Align::CENTER)->modalable(Modal::LARGE)->iconable(LineAwesomeIcon::EDIT);
        $tableContent->linkColumn('id', 'admin.admins.permissions.delete', ['permissionId' => 'id'], Align::CENTER)->iconable(LineAwesomeIcon::TRASH_O)->confirmable('Удаление', 'Вы уверены, что хотите удалить');

        $response = new ResponseBuilder();
        $response->updateTableContent('#adminsPermissionsTable', $tableContent, $request->all());

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
                        'modal' => 'largeModal',
                        'title' => 'Создание прав',
                        'content' => view('admins.permissions.form', [
                            'formAction' => route('admin.admins.permissions.store'),
                            'buttonText' => 'Создать',
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param PermissionRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(PermissionRequest $request)
    {
        $item = $this->permissionsCase->store(['name' => $request->get('name'), 'guard_name' => 'admin']);

        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                    ]
                ],
                'prependTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('admins.permissions.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }


    /**
     * @param $permissionId
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($permissionId)
    {
        $item = $this->permissionsCase->with(['permissions'])->item($permissionId);

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Права не найдены');
            $response->closeModal(Modal::LARGE);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Редактирование прав',
                        'content' => view('admins.permissions.form', [
                            'formAction' => route('admin.admins.permissions.update', ['permissionId' => $permissionId]),
                            'buttonText' => 'Сохранить',
                            'permissionItem' => $item,
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param PermissionUpdateRequest $request
     * @param $permissionId
     * @return JsonResponse
     * @throws Throwable
     * @author Rishat Sultanov
     */
    public function update(PermissionUpdateRequest $request, $permissionId)
    {
        $item = $this->permissionsCase->update($permissionId, $request->all());

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
                        'row' => '.row-' . $permissionId,
                        'content' => view('admins.permissions.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param int $permissionId
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete(int $permissionId)
    {
        $item = $this->permissionsCase->item($permissionId);

        if ($item) {
            $item->delete();
        }

        return response()->json([
            'functions' => [
                'deleteTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $permissionId
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function getAllPermissions(Request $request)
    {
        $query = $request->get('q');

        if (isset($query)) {
            $items = $this->permissionsCase
                ->where('guard_name', 'admin')
                ->whereLike('name', $query)
                ->getListForApi();
        } else {
            $items = $this->permissionsCase
                ->where('guard_name', 'admin')
                ->getListForApi();
        }

        $response = new ResponseBuilder();

        return $response->apiSelectTwo($items);
    }
}
