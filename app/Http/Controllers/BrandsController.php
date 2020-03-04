<?php

namespace App\Http\Controllers;


use App\Http\Utils\ResponseBuilder;
use App\Models\TobaccoBrand;
use App\Services\MediaService\MediaService;
use App\Ui\Attributes\Modal;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Class BrandsController
 * @package App\Http\Controllers
 */
class BrandsController extends Controller
{

    /**
     * @return array|string
     * @throws Throwable
     */
    public function index()
    {
        return view('brands.index')->render();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = TobaccoBrand::query()->withCount('photos')->paginate(30);
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('brands.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#brandsTable', $itemsHtml, $pages);
        return $response->makeJson();
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id)
    {
        $item = TobaccoBrand::query()->with('photos')->find($id);

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Бренд не найден');
            $response->closeModal(Modal::LARGE);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Изображения бренда ' . $item->brand,
                        'content' => view('brands.form', [
                            'item' => $item,
                            'photos' =>  $item->photos->chunk(4)
                        ])->render(),
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
        $mediaService = new MediaService();
        foreach ($request->file('image') as $image) {
            $mediaService->upload($image, TobaccoBrand::class, $itemId);
        }

        $brand = TobaccoBrand::query()->with('photos')->find($itemId);

        return response()->json(([
            'media' => view('brands.media.media_list', ['photos' => $brand->photos])->render(),
        ]));
    }

    /**
     * @param $mediaId
     * @throws Exception
     */
    public function deleteMedia($mediaId)
    {
        (new MediaService())->deleteById($mediaId);
    }
}
