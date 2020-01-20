<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TobaccoProduct;
use Illuminate\Http\JsonResponse;

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
}
