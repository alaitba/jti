<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Utils\ResponseBuilder;
use App\Ui\Attributes\Align;
use App\Ui\Attributes\LineAwesomeIcon;
use App\Ui\Attributes\Modal;
use App\Ui\Components\Portlet\Portlet;
use App\Ui\Components\Table\AjaxLoadableTable;
use App\Ui\Components\Table\TableContent;
use App\Ui\LayoutBuilder;
use App\UseCases\RolesCase;
use Illuminate\View\View;
use App\UseCases\PermissionsCase;
use Throwable;

/**
 * Class RoleController
 * @package App\Http\Controllers
 */
class RoleController extends Controller
{
    /**
     * @var RolesCase
     */
    private $rolesCase;

    /**
     * @var PermissionsCase
     */
    private $permissionsCase;

    /**
     * RoleController constructor.
     * @param RolesCase $rolesCase
     * @param PermissionsCase $permissionsCase
     */
    public function __construct(RolesCase $rolesCase, PermissionsCase $permissionsCase)
    {
        $this->rolesCase = $rolesCase;
        $this->permissionsCase = $permissionsCase;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        $portlet = new Portlet('Роли', LineAwesomeIcon::USERS);
        $portlet->addUrlableIconButoon(route('admin.admins'), LineAwesomeIcon::CIRCLE_LEFT, 'Назад к администраторам');
        $portlet->addModalableIconButton(Modal::LARGE, route('admin.admins.roles.create'), LineAwesomeIcon::PLUS, 'Создать роль');

        $table = new AjaxLoadableTable(route('admin.admins.roles.list'), 'adminsRolesTable');
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
        $items = $this->rolesCase
            ->where('guard_name', 'admin')
            ->getList();

        $tableContent = new TableContent($items);

        $tableContent->textColumn('id', Align::CENTER);
        $tableContent->textColumn('name');
        $tableContent->textColumn('created_at', Align::CENTER);
        $tableContent->linkColumn('id', 'admin.admins.roles.edit', ['roleId' => 'id'], Align::CENTER)->modalable(Modal::LARGE)->iconable(LineAwesomeIcon::EDIT);
        $tableContent->linkColumn('id', 'admin.admins.roles.delete', ['roleId' => 'id'], Align::CENTER)->iconable(LineAwesomeIcon::TRASH_O)->confirmable('Удаление', 'Вы уверены, что хотите удалить');

        $response = new ResponseBuilder();
        $response->updateTableContent('#adminsRolesTable', $tableContent, $request->all());

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
                        'title' => 'Создание роли',
                        'content' => view('admins.roles.form', [
                            'formAction' => route('admin.admins.roles.store'),
                            'buttonText' => 'Создать',
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param RoleRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(RoleRequest $request)
    {
        $item = $this->rolesCase->store(['name' => $request->get('name'), 'guard_name' => 'admin']);

        $permissions = $request->get('permissions');

        if (isset($permissions)) {
            $item->syncPermissions($permissions);
        }

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
                        'content' => view('admins.roles.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }


    /**
     * @param $roleId
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($roleId)
    {
        $item = $this->rolesCase->item($roleId);

        $permissions = $item->getPermissionNames();

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Роль не найдена');
            $response->closeModal(Modal::LARGE);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Редактирование роли',
                        'content' => view('admins.roles.form', [
                            'formAction' => route('admin.admins.roles.update', ['roleId' => $roleId]),
                            'buttonText' => 'Сохранить',
                            'permissions' => $permissions,
                            'roleItem' => $item,
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param RoleUpdateRequest $request
     * @param $roleId
     * @return JsonResponse
     * @throws Throwable
     * @author Rishat Sultanov
     */
    public function update(RoleUpdateRequest $request, $roleId)
    {
        $item = $this->rolesCase->update($roleId, $request->except('permissions'));

        $permissions = $request->get('permissions');

        if (isset($permissions)) {
            $item->syncPermissions($permissions);
        } else {
            $item->syncPermissions([]);
        }

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
                        'row' => '.row-' . $roleId,
                        'content' => view('admins.roles.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param int $roleId
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete(int $roleId)
    {
        $item = $this->rolesCase->item($roleId);

        if ($item) {
            $item->delete();
        }

        return response()->json([
            'functions' => [
                'deleteTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $roleId
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
    public function getAllRoles(Request $request)
    {
        $query = $request->get('q');

        if (isset($query)) {
            $items = $this->rolesCase
                ->where('guard_name', 'admin')
                ->whereLike('name', $query)
                ->getListForApi();
        } else {
            $items = $this->rolesCase
                ->where('guard_name', 'admin')
                ->getListForApi();
        }

        $response = new ResponseBuilder();

        return $response->apiSelectTwo($items);
    }
}
