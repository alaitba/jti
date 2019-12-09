<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = [
        'imageable_type',
        'imageable_id',
        'main_image',
        'client_file_name',
        'original_file_name',
        'conversions',
        'order',
        'size',
        'mime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url', 'url_reverse_proxy'];


    public function getUrlAttribute($value)
    {
        return asset('storage/media/' . $this->original_file_name);
    }

    public function getUrlReverseProxyAttribute($value)
    {
        return '/public/media/' . $this->original_file_name;
    }

    public function setConversionsAttribute($value)
    {
        $this->attributes['conversions'] = json_encode($value);
    }

    public function getConversionsAttribute($value)
    {
        $conversions = json_decode($value, true);

        foreach ($conversions as $k => $conversion)
        {
            $conversions[$k]['url'] = asset('storage/media/' . $conversion['name']);
            $conversions[$k]['url_reverse_proxy'] = '/public/gallery/images/' . $conversion['name'];
        }

        return $conversions;

    }
}
