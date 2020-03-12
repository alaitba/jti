<?php

namespace App\Http\Controllers;

use App\Http\Requests\SliderRequest;
use App\Http\Utils\ResponseBuilder;
use App\Models\Slider;
use App\Services\MediaService\MediaService;
use App\Ui\Attributes\Modal;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Class SliderController
 * @package App\Http\Controllers
 */
class SliderController extends Controller
{
    private $mediaService;

    /**
     * SliderController constructor.
     */
    public function __construct()
    {
        $this->mediaService = new MediaService();
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('slider.index');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function getList(Request $request)
    {
        return $this->showSliders();
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
                        'title' => 'Добавление элемента слайдера',
                        'content' => view('slider.form', [
                            'formAction' => route('admin.slider.store'),
                            'buttonText' => 'Сохранить',
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param SliderRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(SliderRequest $request)
    {
        $slider = Slider::query()->create([
            'link' => $request->input('link'),
            'position' => Slider::query()->max('position') + 1
        ]);
        $file = $request->file('image');
        $this->mediaService->upload($file, Slider::class, $slider->id);
        return $this->showSliders();
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id)
    {
        $item = Slider::query()->find($id);

        if (!$item) {
            $response = new ResponseBuilder();
            $response->showAlert('Ошибка!', 'Элемент слайдра не найден');
            $response->closeModal(Modal::REGULAR);
            return $response->makeJson();
        }

        return response()->json([
            'functions' => [
                'updateModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                        'title' => 'Редактирование элемента слайдера',
                        'content' => view('slider.form', [
                            'formAction' => route('admin.slider.update', $id),
                            'buttonText' => 'Сохранить',
                            'item' => $item,
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param SliderRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(SliderRequest $request, $id)
    {
        $slider = Slider::query()->find($id);
        $slider->link = $request->input('link');
        $slider->save();
        if ($request->has('image'))
        {
            $this->mediaService->deleteForModel(Slider::class, $slider->id);
            $file = $request->file('image');
            $this->mediaService->upload($file, Slider::class, $slider->id);
        }
        return $this->showSliders();
    }


    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function delete($id)
    {
        $item = Slider::query()->find($id);

        if($item) {
            $this->mediaService->deleteForModel(Slider::class, $id);
            $item->delete();
        }

        return $this->showSliders();
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function switchActive($id)
    {
        $item = Slider::query()->find($id);
        $item->active = 1 - $item->active;
        $item->save();

        return response()->json([
            'functions' => [
                'updateTableRow' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'row' => '.row-' . $id,
                        'content' => view('slider.item',['item' => $item])->render()
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param $id
     * @param $direction
     * @return JsonResponse
     * @throws Throwable
     */
    public function move($id, $direction)
    {
        $item = Slider::query()->findOrFail($id);
        if ($direction)
        {
            Slider::query()->where('position', ($item->position - 1))->update(['position' => $item->position]);
            $item->position--;
            $item->save();
        } else {
            Slider::query()->where('position', ($item->position + 1))->update(['position' => $item->position]);
            $item->position++;
            $item->save();
        }
        return $this->showSliders();
    }


    /**
     * @return JsonResponse
     * @throws Throwable
     */
    private function showSliders()
    {
        $items = Slider::query()->orderBy('position', 'ASC')->paginate(25);
        return response()->json([
            'functions' => [
                'closeModal' => [
                    'params' => [
                        'modal' => 'regularModal',
                    ]
                ],
                'updateTableContent' => [
                    'params' => [
                        'selector' => '.ajax-content',
                        'content' => view('slider.list', [
                            'items' => $items,
                        ])->render(),
                        'pagination' => view('layouts.pagination', [
                            'links' => $items->appends(request()->all())->links('pagination.bootstrap-4'),
                        ])->render(),
                    ]
                ]
            ]
        ]);
    }
}
