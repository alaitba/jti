<?php


namespace App\Exports;

use App\Models\QuizResult;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class TestQueryExport implements FromQuery
{
    use Exportable;

    public function query()
    {
        return QuizResult::query()
            ->orderBy('created_at', 'DESC')
            ->select('quiz_results.id');
    }
}
