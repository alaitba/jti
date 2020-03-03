<?php namespace App\Services\LocalisationService;

use Exception;

// models
use App\Models\Localisation;
use App\Models\LocalisationGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class LocalisationService
 * @package App\Services\LocalisationService
 */
class LocalisationService {

    private $localisation;
    private $group;

    public function __construct()
    {
        $this->localisation = new Localisation();
        $this->group = new LocalisationGroup();
    }


    /**
     * @param int $groupId
     * @return LengthAwarePaginator
     */
    public function localisationList(int $groupId): LengthAwarePaginator
    {
        return $this->localisation->with('group')->where('group_id', $groupId)->paginate(25);
    }


    /**
     * @param array $fields
     * @return mixed
     */
    public function localisationStore(array $fields): Localisation
    {
        return $this->localisation->create($fields);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function localisationGet(int $id)
    {
        $localisation = $this->localisation->find($id);

        if (!$localisation)
        {
            throw new Exception("Localisation:  $id not found");
        }

        return $localisation;
    }

    /**
     * @param int $id
     * @param array $fields
     * @return mixed
     * @throws Exception
     */
    public function localisationUpdate(int $id, array $fields)
    {
        $localisation = $this->localisation->find($id);

        if (!$localisation)
        {
            throw new Exception("Localisation:  $id not found");
        }

        $localisation->update($fields);

        return $localisation;
    }

    /**
     * @param array $fields
     * @return mixed
     * @throws Exception
     */
    public function groupStore(array $fields)
    {
        $group = $this->group->create($fields);


        return $this->groupGet($group->id);
    }

    /**
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function groupList(array $relations = []): LengthAwarePaginator
    {
        return $this->group->withCount('localisations')->with($relations)->orderBy('name')->paginate(25);
    }



    /**
     * @param int $id
     * @return LocalisationGroup
     * @throws Exception
     */
    public function groupGet(int $id): LocalisationGroup
    {
        $group =  $this->group->withCount('localisations')->find($id);


        if (!$group)
        {
            throw new Exception("Group:  $id not found");
        }

        return $group;
    }

    /**
     * @param int $id
     * @param array $fields
     * @return mixed
     * @throws Exception
     */
    public function groupUpdate(int $id, array $fields)
    {
        $group = $this->group->find($id);

        if (!$group)
        {
            throw new Exception("Group:  $id not found");
        }

        $group->update($fields);

        return $this->groupGet($group->id);
    }

    /**
     * Получение перевода
     * @param string $name
     * @param string $locale
     * @return string
     * @throws Exception
     */
    public function getTranslation(string $name, string $locale): string
    {
        $model = $this->localisation->where('slug', $name)->first();

        if ($model)
        {
            return $model->getTranslation('values', $locale);
        }

        throw new Exception("Localisation $name not found" );
    }

    /**
     * Получение переводов для API.
     * @param string $locale
     * @return array
     */
    public function getLocalisationsForApi(string $locale)
    {
        $data = $this->localisation->orderBy('group_id')->get()->toArray();


        $localisations = [];

        foreach ($data as $item)
        {
            if (isset($item['values'][$locale]))
            {
                $localisations[$item['name']] = $item['values'][$locale];
            }
        }

        return $localisations;
    }


}
