<?php namespace App\Services\SectionService;


use App\Models\Segment;

class SectionService {

    public function get(string $name)
    {
        return Segment::firstOrCreate(['alias' => $name]);
    }

    public function set(string $name, array $data)
    {

        $segment = $this->get($name);
        $segment->data = $data;
        $segment->save();

        return $segment->data;
    }
}
