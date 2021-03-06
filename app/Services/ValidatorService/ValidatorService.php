<?php


namespace App\Services\ValidatorService;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Class ValidatorService
 * @package App\Services\ValidatorService
 */
class ValidatorService
{
    /**
     * @param array $request
     * @param array $rules
     * @return bool|JsonResponse
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
