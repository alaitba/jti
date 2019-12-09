<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;

use App\Contracts\Localisation as LocalisationContract;

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

    public function __construct(array $attributes = [])
    {

        parent::__construct($attributes);
    }

    public $translatable = ['values'];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function group()
    {
        return $this->belongsTo(LocalisationGroup::class, 'group_id');
    }

    public function getInjectCode()
    {
        return "{{ st_trans('$this->slug') }}";
    }
}
