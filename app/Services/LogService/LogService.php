<?php


namespace App\Services\LogService;


use Exception;
use Illuminate\Support\Facades\Log;

class LogService
{
    public static function logException(Exception $e)
    {
        Log::error('================');
        Log::error($e->getMessage());
        Log::error($e->getFile() . ': ' . $e->getLine());
        Log::error($e->getTraceAsString());
        Log::error('================');
    }
}
