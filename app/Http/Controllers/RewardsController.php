<?php

namespace App\Http\Controllers;


use App\Http\Utils\ResponseBuilder;
use App\Models\Reward;
use App\Services\MediaService\MediaService;
use App\Ui\Attributes\Modal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class RewardsController extends Controller
{

    /**
     * @return array|string
     * @throws Throwable
     */
    public function index()
    {
        $updated = Reward::withTrashed()->latest('updated_at')->first();
        return view('rewards.index', [
            'lastUpdate' => $updated->updated_at_string ?? '-'
        ])->render();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        $items = Reward::withoutTrashed()->withCount('photos')->paginate(30);
        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= view('rewards.table_row', ['item' => $item])->render();
        }
        $pages = $items->appends($request->all())->links('pagination.bootstrap-4');
        $response = new ResponseBuilder();
        $response->updateTableContentHtml('#rewardsTable', $itemsHtml, $pages);
        return $response->makeJson();
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id)
    {
        $item = Reward::withoutTrashed()->with('photos')->find($id);

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Приз не найден');
            $response->closeModal(Modal::LARGE);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'largeModal',
                        'title' => 'Редактирование приза',
                        'content' => view('rewards.form', [
                            'formAction' => route('admin.rewards.update', ['id' => $id]),
                            'buttonText' => 'Сохранить',
                            'item' => $item,
                            'photos' =>  $item->photos->chunk(2)
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(Request $request, $id)
    {
        $item = Reward::withoutTrashed()->withCount('photos')->find($id);
        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Приз не найден');
            $response->closeModal(Modal::LARGE);
            return $response->makeJson();
        }
        $item->fill($request->only(['name', 'description']));
        $item->save();
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
                        'row' => '.row-' . $id,
                        'content' => view('rewards.table_row', ['item' => $item])->render()
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
            $mediaService->upload($image, Reward::class, $itemId);
        }

        $reward = Reward::withoutTrashed()->with('photos')->find($itemId);

        return response()->json(([
            'media' => view('rewards.media.media_list', ['photos' => $reward->photos])->render(),
        ]));
    }

    /**
     * @param $mediaId
     * @throws \Exception
     */
    public function deleteMedia($mediaId)
    {
        (new MediaService())->deleteById($mediaId);
    }
}
