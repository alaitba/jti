<?php

namespace App\Http\Controllers;


use App\Http\Utils\ResponseBuilder;
use App\Services\LogService\LogService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Class HolidaysController
 * @package App\Http\Controllers
 */
class HolidaysController extends Controller
{
    const JS_FORMAT = 'j/n/Y';
    /**
     * @return array|string
     * @throws Throwable
     */
    public function index()
    {
        $curYear = date('Y');
        if (!Storage::disk('local')->exists('data/holidays' . $curYear))
        {
            $this->generateHolidays($curYear);
        }
        if (!Storage::disk('local')->exists('data/holidays' . ($curYear + 1)))
        {
            $this->generateHolidays($curYear + 1);
        }
        $days = Storage::disk('local')->get('data/holidays' . $curYear);
        $days = json_decode($days);
        $daysNext = Storage::disk('local')->get('data/holidays' . ($curYear + 1));
        $daysNext = json_decode($daysNext);
        $holidays = '';
        foreach (array_merge($days, $daysNext) as $day)
        {
            $date = Carbon::createFromFormat(self::JS_FORMAT, $day)->format('Y, n-1, j');
            $holidays .= '{color: "#ff0000", startDate: new Date(' . $date . '), endDate: new Date(' . $date . ')},';
        }

        return view('holidays.index', [
            'holidays' => $holidays
        ])->render();
    }

    /**
     * @param int $curYear
     */
    private function generateHolidays(int $curYear)
    {
        $holidays = [];
        $curDate = Carbon::parse($curYear . '-01-01')->startOfYear();
        $num = $curDate->isLeapYear() ? 366 : 365;
        for ($i = 1; $i <= $num; $i++)
        {
            if ($curDate->isWeekend())
            {
                $holidays []= $curDate->format(self::JS_FORMAT);
            }
            $curDate->addDay();
        };
        Storage::disk('local')->put('data/holidays' . $curYear, json_encode($holidays));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $response = new ResponseBuilder();
        try {
            $curYear = date('Y');
            $dates = explode(',', $request->input('dates'));
            $currentDays = [];
            $nextDays = [];
            foreach ($dates as $date) {
                $curDate = Carbon::parse($date);
                if ($curDate->isCurrentYear()) {
                    $currentDays [] = $curDate->format(self::JS_FORMAT);
                } elseif ($curDate->isNextYear()) {
                    $nextDays [] = $curDate->format(self::JS_FORMAT);
                }
            }
            Storage::disk('local')->put('data/holidays' . $curYear, json_encode($currentDays));
            Storage::disk('local')->put('data/holidays' . ($curYear + 1), json_encode($nextDays));
            $response->showAlert('Выполнено.', 'Выходные и праздники успешно сохранены', 'success');
        } catch (\Exception $e) {
            $response->showAlert('Ошибка.', 'Не удалось сохранить выходные и праздники', 'error');
            LogService::logException($e);
        }
        return $response->makeJson();
    }
}
