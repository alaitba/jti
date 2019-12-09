<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Contracts\LocalisationGroup as LocalisationGroupContract;
class LocalisationGroup extends Model implements LocalisationGroupContract
{
    public $timestamps = false;
    protected $table = 'localisation_groups';
    protected $fillable = ['name'];

    public function localisations()
    {
        return $this->hasMany(Localisation::class, 'group_id');
    }
}
