<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


// requests
use App\Http\Requests\WysiwygRequest;

// services
use App\Services\UploaderService\UploaderService;
//use MediaService;
use App\Services\MediaService\MediaService;
use Throwable;

class WysiwygController extends Controller
{

    private $breadCrumbsData;
    private $uploaderService;
    private $mediaService;


    public function __construct()
    {
        $this->uploaderService = new UploaderService();
        $this->mediaService = new MediaService();
    }

    /**
     * Список файлов и каталогов
     * @param Request $request
     * @param UploaderService $uploaderService
     * @return JsonResponse
     * @throws Throwable
     */
    public function objects(Request $request, UploaderService $uploaderService)
    {
        $groupId = $request->input('parent_id') ?? null;

        return $this->generalResponse($uploaderService, $groupId, $request->all());
    }

    /**
     * Json ответ для модального окна
     * @param UploaderService $uploaderService
     * @param $groupId
     * @param array $request
     * @return JsonResponse
     * @throws Throwable
     */
    private function generalResponse(UploaderService $uploaderService, $groupId, array $request = [])
    {
        $folders = $uploaderService->folderList($groupId);
        $files = $uploaderService->uploadList($groupId, $request);

        $mime = (isset($request['mime'])) ? $request['mime'] : 'all';

        $folderCreateUrl = route('admin.wysiwyg.folder.create', ['parent_id' => $groupId]);
        $breadCrumbs = $this->getBreadCrumbs($groupId);

        return response()->json([


            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => "manageFolderModal"
                    ]
                ],

                'openModal' => [
                    'params' => [
                        'modal' => "editorModal"
                    ]
                ],

                'updateModal' => [
                    'params' => [
                        'modal' => 'editorModal',
                        'title' => 'Загрузки',
                        'content' => view('wysiwyg.files', [
                            'folders' => $folders,
                            'files' => $files,
                            'mime' => $mime,
                            'folderCreateUrl' => $folderCreateUrl,
                            'breadCrumbs' => $breadCrumbs,
                            'uploadUrl' => route('admin.wysiwyg.file.store', ['parent_id' => $groupId])
                        ])->render()
                    ]
                ]
            ]
        ]);
    }


    /**
     * Контент модалки создания/редактирования папки
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function folderCreate(Request $request)
    {
        $parentId = $request->input('parent_id');

        return response()->json([

            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'manageFolderModal',
                        'title' => 'Создание каталога',
                        'content' => view('wysiwyg.partials.folder_form', [
                            'legend' => 'Создание каталога',
                            'formAction' => route('admin.wysiwyg.folder.store', ['parent_id' => $parentId]),
                            'submitBtnText' => 'Создать'
                        ])->render()
                    ]
                ]
            ],
        ]);
    }

    /**
     * Создание каталога
     * @param WysiwygRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function folderStore(WysiwygRequest $request)
    {
        $folder = $this->uploaderService->folderStore($request->input('name'), $request->input('parent_id'));

        return $this->generalResponse($this->uploaderService, $folder->parent_id, $request->all());
    }

    /**
     * Контент модалки для редактирования папки
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function folderEdit($id)
    {
        $folder = $this->uploaderService->folderById($id);
        return response()->json([

            'functions' => [
                'updateModal' => [
                    'params' => [
                        'title' => 'Редактирование каталога',
                        'modal' => 'manageFolderModal',
                        'content' => view('wysiwyg.partials.folder_form', [
                            'legend' => 'Редактирование каталога',
                            'folder' => $folder,
                            'formAction' => route('admin.wysiwyg.folder.update', ['id' => $id]),
                            'submitBtnText' => 'Сохранить'
                        ])->render()
                    ]
                ]
            ],

        ]);
    }

    /**
     * Обновление имени папки
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function folderUpdate(Request $request, $id)
    {
        $folder = $this->uploaderService->folderRename($id, $request->input('name'));

        return response()->json([

            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'manageFolderModal'
                    ]
                ],

                'updateTableRow' => [
                    'params' => [
                        'selector' => '#mediaFilesTable',
                        'row' => '.folder-row-' . $id,
                        'content' => view('wysiwyg.partials.folder_item', ['folder' => $folder])->render()
                    ]
                ]
            ],

        ]);
    }

    /**
     * Удаление папки
     * @param $id
     * @return JsonResponse
     */
    public function folderDelete($id)
    {
        $this->uploaderService->folderDelete($id);

        return response()->json([
            'functions' => [
                'deleteTableRow' => [
                    'params' => [
                        'selector' => "#mediaFilesTable",
                        'row' => '.folder-row-' . $id
                    ]
                ]
            ]
        ]);


    }

    /**
     * Сохранение папки
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function fileStore(Request $request)
    {
        $parentId = $request->input('parent_id');

        if ($request->hasFile('uploads'))
        {
            foreach ($request->file('uploads') as $file)
            {
                $this->uploaderService->upload($file, $parentId, 'wisywyg');
            }
        }

        return $this->generalResponse($this->uploaderService, $parentId, $request->all());
    }

    public function imageEdit(int $id)
    {
        $image = $this->uploaderService->uploadById($id);

        if ($image)
        {
            $cancelUrl = route('admin.wysiwyg.objects', ['parent_id' => $image->group_id]);
            return response()->json([
                'functions' => [
                    'updateModal' => [
                        'params' => [
                            'modal' => 'editImageModal',
                            'title' => 'Настройка изображения',
                            'content' => view('common.media.crop.index', [
                                'formAction' => route('admin.wysiwyg.image.update', ['id' => $id]),
                                'imgPath' => asset('storage/uploads/' . $image->getOriginal('original_file_name') . '?' . uniqid()),

                            ])->render()
                        ]
                    ],

                    'initCrop' => [
                        'params' => []
                    ]
                ],

            ]);
        }
    }

    public function imageUpdate(Request $request, $id)
    {
        $upload = $this->uploaderService->uploadById($id);
        $path = storage_path('app/public/uploads/' . $upload->getOriginal('original_file_name'));
        $this->mediaService->cropImage($path, $request->all());

        return response()->json([

            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'editImageModal'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Удаление файла
     * @param $id
     * @return JsonResponse
     */
    public function fileDelete($id)
    {
        $this->uploaderService->uploadDelete($id);

        return response()->json([
            'functions' => [
                'deleteTableRow' => [
                    'params' => [
                        'selector' => "#mediaFilesTable",
                        'row' => '.file-row-' . $id
                    ]
                ]
            ]
        ]);
    }


    /**
     * Герерация хлебных крошек для модалки
     * @param null $parentId
     * @return string
     */
    private function getBreadCrumbs($parentId = null)
    {
        $data = '<a data-url="' . route('admin.wysiwyg.objects', ['parent_id' => null]) . '" class="handle-click" data-type="ajax-get" data-block-element="#editorModal .modal-body" style="cursor: context-menu;"><i class="fa fa-folder-open-o"></i> Корень</a>';

        if (!$parentId)
        {
            return $data;
        }

        $this->buildBreadCrumbs($parentId);

        $data .= $this->breadCrumbsData;

        return $data;


    }

    /**
     * Рекурсия для создания хлебных крошек
     * @param $id
     */
    private function buildBreadCrumbs($id)
    {

        $folder = $this->uploaderService->folderById($id);

        $this->breadCrumbsData = ' <i class="fa fa-angle-right"></i> <a data-url="' . route('admin.wysiwyg.objects', ['parent_id' => $folder->id]) . '" class="handle-click" data-type="ajax-get" data-block-element="#editorModal .modal-body" style="cursor: context-menu;"><i class="fa fa-folder-open-o"></i> ' . $folder->name . '</a>' . $this->breadCrumbsData;

        if ($folder->parent_id)
        {
            $this->buildBreadCrumbs($folder->parent_id);
        }


    }

    /**
     * Формирование html для инжекта контента в визивиг
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function inject($id)
    {
        $file = $this->uploaderService->uploadById($id);

        $template = $this->getInjectTemplate($file->type);

        return response()->json([
            'functions' => [
                'editorInject' => [
                    'params' => [
                        'content' => view($template, ['file' => $file])->render()
                    ]
                ],

                'closeModal' => [
                    'params' => [
                        'modal' => "editorModal"
                    ]
                ]
            ],
        ]);
    }

    /**
     * Определение вьюхи для инжекта в зависимости от типа файла
     * @param $type
     * @return string
     */
    private function getInjectTemplate($type)
    {
        switch ($type)
        {
            case 'file':
                return 'wysiwyg.partials.inject_file';
                break;

            case 'image':
                return 'wysiwyg.partials.inject_image';
                break;
        }
    }
}
