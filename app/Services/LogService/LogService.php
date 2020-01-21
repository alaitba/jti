<?php


namespace App\Services\LogService;


use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class LogService
 * @package App\Services\LogService
 */
class LogService
{
    /**
     * @param Exception $e
     */
    public static function logException(Exception $e)
    {
        Log::error('================');
        Log::error($e->getMessage());
        Log::error($e->getFile() . ': ' . $e->getLine());
        Log::error($e->getTraceAsString());
        Log::error('================');
    }

    /**
     * @param $msg
     */
    public static function logInfo($msg)
    {
        Log::info('=================');
        Log::info($msg);
        Log::info('=================');
    }
}
