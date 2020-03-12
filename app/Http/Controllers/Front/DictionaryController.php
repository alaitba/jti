<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\TobaccoProduct;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class DictionaryController
 * @package App\Http\Controllers\Front
 */
class DictionaryController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getTobaccoProducts()
    {
        $items = TobaccoProduct::withoutTrashed()->get(['product_code', 'brand', 'sku'])->groupBy('brand')->toArray();
        return response()->json([
            'status' => 'ok',
            'data' => $items
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws FileNotFoundException
     */
    public function getHolidays(Request $request)
    {
        $year = $request->input('year', date('Y'));
        if (!Storage::disk('local')->exists('data/holidays' . $year)) {
            return response()->json([
                'status' => 'error',
                'message' => 'no_data'
            ], 404);
        }
        return response()->json([
            'status' => 'ok',
            'holidays' => json_decode(Storage::disk('local')->get('data/holidays' . $year))
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getSlider()
    {
        $items = [];
        Slider::with('image')->orderBy('position', 'ASC')->get()->each(function (Slider $item) use (&$items) {
            $items [] = [
                'image' => $item->image->url,
                'link' => $item->link
            ];
        });
        return response()->json([
            'status' => 'ok',
            'data' => $items
        ]);
    }
}
