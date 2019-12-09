<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AdminsRequest;
use App\UseCases\AdminCase;

/**
 * Ui-Kit
 */

// Components
use App\Ui\Components\Portlet\Portlet;
use App\Ui\Components\Table\AjaxLoadableTable;
use App\Ui\Components\Table\TableContent;

// Attributes
use App\Ui\Attributes\Modal;
use App\Ui\Attributes\LineAwesomeIcon;
use App\Ui\Attributes\Align;

// Layout
use App\Ui\LayoutBuilder;
use App\Http\Utils\ResponseBuilder;
use Illuminate\View\View;
use Throwable;

/**
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * @var AdminCase
     */
    private $adminCase;

    /**
     * AdminController constructor.
     * @param AdminCase $adminCase
     */
    public function __construct(AdminCase $adminCase)
    {
        $this->adminCase = $adminCase;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function index()
    {
        $portlet = new Portlet('Администраторы', LineAwesomeIcon::USERS);
        $portlet->addUrlableIconButoon(route('admin.admins.permissions'), LineAwesomeIcon::BAN, 'Права');
        $portlet->addUrlableIconButoon(route('admin.admins.roles'), LineAwesomeIcon::GROUP, 'Роли');
        $portlet->addModalableIconButton(Modal::LARGE, route('admin.admins.create'), LineAwesomeIcon::PLUS, 'Создать администратора');

        $table = new AjaxLoadableTable(route('admin.admins.list'), 'adminsTable');
        $table->addColumn('#', Align::CENTER, '50');
        $table->addColumn('Имя', Align::LEFT);
        $table->addColumn('E-mail', Align::LEFT);
        $table->addColumn(LineAwesomeIcon::POWER_OFF, Align::CENTER, 50);
        $table->addColumn(LineAwesomeIcon::MAGIC, Align::CENTER, 50);
        $table->addColumn(LineAwesomeIcon::CODE, Align::CENTER, 50);
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
        $items = $this->adminCase->getList();

        $tableContent = new TableContent($items);

        $tableContent->textColumn('id', Align::CENTER);
        $tableContent->textColumn('name');
        $tableContent->textColumn('email');
        $tableContent->textColumn('active', Align::CENTER)->iconableBoolean(LineAwesomeIcon::POWER_OFF, 'green', 'red');
        $tableContent->textColumn('super_user', Align::CENTER)->iconableBoolean(LineAwesomeIcon::MAGIC, 'green', 'red');
        $tableContent->textColumn('develop', Align::CENTER)->iconableBoolean(LineAwesomeIcon::CODE, 'green', 'red');
        $tableContent->linkColumn('id', 'admin.admins.edit', ['id' => 'id'], Align::CENTER)->modalable(Modal::LARGE)->iconable(LineAwesomeIcon::EDIT);
        $tableContent->linkColumn('id', 'admin.admins.delete', ['id' => 'id'], Align::CENTER)->iconable(LineAwesomeIcon::TRASH_O)->confirmable('Удаление', 'Вы уверены, что хотите удалить');

        $response = new ResponseBuilder();
        $response->updateTableContent('#adminsTable', $tableContent, $request->all());

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
                        'content' => view('admins.form', [
                            'formAction' => route('admin.admins.store'),
                            'buttonText' => 'Создать',
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    public function store(AdminsRequest $request)
    {
        $data = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'password' => $request->get('password'),
            'active' => 1,
            'super_user' => 0,
            'develop' => 0,
        ];

        $admin = $this->adminCase->store($data);

        $roles = $request->get('roles');

        if (isset($roles)) {
            $admin->syncRoles($roles);
        }

        $tableContent = new TableContent($admin);
        $tableContent->textColumn('id', Align::CENTER);
        $tableContent->textColumn('name');
        $tableContent->textColumn('email');
        $tableContent->textColumn('active', Align::CENTER)->iconableBoolean(LineAwesomeIcon::POWER_OFF, 'green', 'red');
        $tableContent->textColumn('super_user', Align::CENTER)->iconableBoolean(LineAwesomeIcon::MAGIC, 'green', 'red');
        $tableContent->textColumn('develop', Align::CENTER)->iconableBoolean(LineAwesomeIcon::CODE, 'green', 'red');
        $tableContent->linkColumn('id', 'admin.admins.edit', ['id' => 'id'], Align::CENTER)->modalable(Modal::LARGE)->iconable(LineAwesomeIcon::EDIT);
        $tableContent->linkColumn('id', 'admin.admins.delete', ['id' => 'id'], Align::CENTER)->iconable(LineAwesomeIcon::TRASH_O)->confirmable('Удаление', 'Вы уверены, что хотите удалить');

        $response = new ResponseBuilder();
        $response->prependTableRow($tableContent, '#adminsTable');
        $response->closeModal(Modal::LARGE);

        return $response->makeJson();
    }


    /**
     * @param $adminId
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($adminId)
    {
        $item = $this->adminCase->item($adminId);

        $roles = $item->getRoleNames();

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Админ не найден');
            $response->closeModal(Modal::LARGE);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Редактирование роли',
                        'content' => view('admins.form', [
                            'formAction' => route('admin.admins.update', ['id' => $adminId]),
                            'buttonText' => 'Сохранить',
                            'roles' => $roles,
                            'item' => $item,
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    public function update(AdminsRequest $request, $adminId)
    {
        $data = [
            'email' => $request->get('email'),
            'name' => $request->get('name'),
            'password' => $request->get('password'),
            'active' => $request->has('active'),
            'super_user' => $request->has('super_user')
        ];

        $item = $this->adminCase->update($adminId, $data);

        $roles = $request->get('roles');

        if (isset($roles)) {
            $item->syncRoles($roles);
        } else {
            $item->syncRoles([]);
        }

        $tableContent = new TableContent($item);
        $tableContent->textColumn('id', Align::CENTER);
        $tableContent->textColumn('name');
        $tableContent->textColumn('email');
        $tableContent->textColumn('active', Align::CENTER)->iconableBoolean(LineAwesomeIcon::POWER_OFF, 'green', 'red');
        $tableContent->textColumn('super_user', Align::CENTER)->iconableBoolean(LineAwesomeIcon::MAGIC, 'green', 'red');
        $tableContent->textColumn('develop', Align::CENTER)->iconableBoolean(LineAwesomeIcon::CODE, 'green', 'red');
        $tableContent->linkColumn('id', 'admin.admins.edit', ['id' => 'id'], Align::CENTER)->modalable(Modal::LARGE)->iconable(LineAwesomeIcon::EDIT);
        $tableContent->linkColumn('id', 'admin.admins.delete', ['id' => 'id'], Align::CENTER)->iconable(LineAwesomeIcon::TRASH_O)->confirmable('Удаление', 'Вы уверены, что хотите удалить');

        $response = new ResponseBuilder();
        $response->updateTableRow($tableContent, '#adminsTable', $adminId);
        $response->closeModal(Modal::LARGE);

        return $response->makeJson();
    }

    /**
     * @param int $adminId
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete(int $adminId)
    {
        $item = $this->adminCase->item($adminId);

        if($item) {
            $item->delete();
        }

        return response()->json([
            'functions' => [
                'deleteTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $adminId
                    ]
                ]
            ]
        ]);
    }
}
