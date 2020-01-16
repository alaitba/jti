<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\morphMany;
use Illuminate\Database\Eloquent\Relations\morphOne;

/**
 * Trait HasMedia
 * @package App\Traits
 */
trait HasMedia
{
    /**
     * @return mixed
     */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['url_endpoint', 'url_endpoint_reverse_proxy']));

        return parent::getArrayableAppends();
    }

    /**
     * @return string
     */
    public function getUrlEndpointAttribute()
    {
        return asset('storage/media/');
    }

    /**
     * @return string
     */
    public function getUrlEndpointReverseProxyAttribute()
    {
        return '/public/media/';
    }

    /**
     * Model may have media.
     *
     * @return morphMany
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'imageable');
    }

    /**
     * Model may have one mainImage.
     *
     * @return morphOne
     */
    public function mainImage()
    {
        return $this->morphOne(Media::class, 'imageable')->where('main_image', 1);
    }
}
