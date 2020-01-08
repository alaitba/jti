<?php


namespace App\Services\ValidatorService;


use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ValidatorService
{
    /**
     * @param array $request
     * @param array $rules
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public static function validateRequest(array $request, array $rules)
    {
        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation_failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
        return true;
    }
}
