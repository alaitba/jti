<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;

use App\Contracts\Localisation as LocalisationContract;

/**
 * Class Localisation
 * @package App\Models
 */
class Localisation extends Model implements LocalisationContract
{
    use Sluggable;
    use HasTranslations;

    public $timestamps = false;
    protected $table = 'localisations';
    protected $fillable = [
        'group_id',
        'name',
        'slug',
        'values',
    ];

    /**
     * Localisation constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {

        parent::__construct($attributes);
    }

    public $translatable = ['values'];

    /**
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * @return BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(LocalisationGroup::class, 'group_id');
    }

    /**
     * @return string
     */
    public function getInjectCode()
    {
        return "{{ st_trans('$this->slug') }}";
    }
}
