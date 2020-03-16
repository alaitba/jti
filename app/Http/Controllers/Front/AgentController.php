<?php

namespace App\Http\Controllers\Front;



use App\Http\Controllers\Controller;
use App\Providers\JtiApiProvider;
use Illuminate\Http\JsonResponse;

/**
 * Class AgentController
 * @package App\Http\Controllers\Front
 */
class AgentController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function callAgent()
    {
        return response()->json([
            'status' => JtiApiProvider::ordersOos() ? 'ok' : 'failed',
        ]);
    }

}
