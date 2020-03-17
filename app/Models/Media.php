<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Media
 * @property array conversions
 * @property bool main_image
 * @property mixed imageable_type
 * @property int imageable_id
 * @property int id
 * @property string original_file_name
 * @property string url
 * @package App\Models
 */
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


    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        return asset('storage/media/' . $this->original_file_name);
    }

    /**
     * @return string
     */
    public function getUrlReverseProxyAttribute()
    {
        return '/public/media/' . $this->original_file_name;
    }

    /**
     * @param $value
     */
    public function setConversionsAttribute($value)
    {
        $this->attributes['conversions'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     */
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
