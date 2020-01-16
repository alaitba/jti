<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Contracts\LocalisationGroup as LocalisationGroupContract;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class LocalisationGroup
 * @package App\Models
 */
class LocalisationGroup extends Model implements LocalisationGroupContract
{
    public $timestamps = false;
    protected $table = 'localisation_groups';
    protected $fillable = ['name'];

    /**
     * @return HasMany
     */
    public function localisations()
    {
        return $this->hasMany(Localisation::class, 'group_id');
    }
}
