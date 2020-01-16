<?php namespace App\Services\SectionService;


use App\Models\Segment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SectionService
 * @package App\Services\SectionService
 */
class SectionService {

    /**
     * @param string $name
     * @return Builder|Model
     */
    public function get(string $name)
    {
        return Segment::query()->firstOrCreate(['alias' => $name]);
    }

    /**
     * @param string $name
     * @param array $data
     * @return array|mixed
     */
    public function set(string $name, array $data)
    {

        $segment = $this->get($name);
        $segment->data = $data;
        $segment->save();

        return $segment->data;
    }
}
