<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// services
use App\Services\LocalisationService\LocalisationService;

// requests
use App\Http\Requests\LocalisationGroupRequest;
use App\Http\Requests\LocalisationGroupItemRequest;
use Illuminate\View\View;
use Throwable;

class LocalisationController extends Controller
{

    private $localisation;

    /**
     * LocalisationController constructor.
     * @param LocalisationService $localisation
     */
    public function __construct(LocalisationService $localisation)
    {
        $this->localisation = $localisation;
    }

    /**
     * @return Factory|View
     */
    public function groups(): View
    {
        return view('settings.localisation.groups.index', [
            'title' => 'Группы локализаций'
        ]);
    }

    /**
     * @return JsonResponse
     * @throws Throwable
     */
    public function groupList(): JsonResponse
    {
        $items = $this->localisation->groupList();

        return response()->json([
            'functions' => [
                'updateTableContent' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('settings.localisation.groups.list', [
                            'items' => $items,
                        ])->render(),
                        'pagination' => view('layouts.pagination', [
                            'links' => $items->links('pagination.bootstrap-4'),
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
    public function groupCreate(): JsonResponse
    {
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Создание группы',
                        'content' => view('settings.localisation.groups.form', [
                            'formAction' => route('admin.settings.localisation.groups.store'),
                            'buttonText' => 'Создать'
                        ])->render(),
                    ]
                ],


            ]
        ]);
    }


    /**
     * @param LocalisationGroupRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function groupStore(LocalisationGroupRequest $request): JsonResponse
    {
        $item = $this->localisation->groupStore($request->all());

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
                        'content' => view('settings.localisation.groups.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function groupEdit(int $id): JsonResponse
    {
        $item = $this->localisation->groupGet($id);

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Редактирование группы',
                        'content' => view('settings.localisation.groups.form', [
                            'item' => $item,
                            'formAction' => route('admin.settings.localisation.groups.update', ['groupId' => $id]),
                            'buttonText' => 'Сохранить'
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }


    /**
     * @param LocalisationGroupRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function groupUpdate(LocalisationGroupRequest $request, int $id): JsonResponse
    {
        $item = $this->localisation->groupUpdate($id, $request->all());

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
                        'row' => '.row-' . $id,
                        'content' => view('settings.localisation.groups.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    public function groupsItems(int $groupId)
    {
        $group = $this->localisation->groupGet($groupId);
        return view('settings.localisation.groups.items.index', [
            'title' => "[$group->name] переводы",
            'group' => $group
        ]);
    }

    public function groupsItemsList(int $groupId)
    {
        $items = $this->localisation->localisationList($groupId);

        return response()->json([
            'functions' => [
                'updateTableContent' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('settings.localisation.groups.items.list', [
                            'items' => $items,
                        ])->render(),
                        'pagination' => view('layouts.pagination', [
                            'links' => $items->links('pagination.bootstrap-4'),
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    public function groupsItemsCreate(int $groupId)
    {
        $group = $this->localisation->groupGet($groupId);
        $locales = config('project.locales');
        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Создание перевода для группы ' . $group->name,
                        'content' => view('settings.localisation.groups.items.form', [
                            'locales' => $locales,
                            'formAction' => route('admin.settings.localisation.groups.items.store', ['groupId' => $groupId]),
                            'buttonText' => 'Создать'
                        ])->render(),
                    ]
                ],


            ]
        ]);
    }

    public function groupsItemsStore(LocalisationGroupItemRequest $request, int $groupId)
    {
        $request->merge(['group_id' => $groupId]);

        $item = $this->localisation->localisationStore($request->all());


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
                        'content' => view('settings.localisation.groups.items.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    public function groupsItemsEdit(int $groupId, int $itemId)
    {
        $item = $this->localisation->localisationGet($itemId);
        $group = $this->localisation->groupGet($groupId);
        $locales = config('project.locales');

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Редактирование перевода для группы ' . $group->name,
                        'content' => view('settings.localisation.groups.items.form', [
                            'locales' => $locales,
                            'item' => $item,
                            'formAction' => route('admin.settings.localisation.groups.items.update', ['groupId' => $groupId, 'itemId' => $itemId]),
                            'buttonText' => 'Сохранить'
                        ])->render(),
                    ]
                ],


            ]
        ]);
    }

    public function groupsItemsUpdate(Request $request, int $groupId, int $itemId)
    {
        $item = $this->localisation->localisationUpdate($itemId, $request->all());

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
                        'row' => '.row-' . $itemId,
                        'content' => view('settings.localisation.groups.items.item', ['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    public  function groupsItemsShow(int $groupId, int $itemId)
    {
        $item = $this->localisation->localisationGet($itemId);
        $locales = config('project.locales');

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Переводы: ' . $item->name,
                        'content' => view('settings.localisation.groups.items.show', [
                            'locales' => $locales,
                            'item' => $item,
                        ])->render(),
                    ]
                ],


            ]
        ]);
    }


}
