<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\TobaccoProduct;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
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
