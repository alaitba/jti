<?php

namespace App\Services\UploaderService;


use Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;

// models
use App\Models\Upload;

/**
 * Сервис отвечает за управление загружаемыми файлами. Например через визивиг.
 * Загружаемые файлы можно группировать в каталоги, за это отвечает модель Category.
 *
 *
 * Class UploaderService
 * @package App\Services\UploaderService
 */
class UploaderService {


    /**
     * Овнер для категории
     * @var string
     */
    private $categoryOwner;

    /**
     * Модель Category
     * @var Category
     */
    private $category;

    /**
     * Модель Upload
     * @var Upload
     */
    private $upload;

    private $allowedFileMimes;


    public function __construct()
    {


        $this->categoryOwner = 'wysiwyg';


        /**
         * Создание инстанса модели Category
         */
        $this->category = new Category();

        /**
         * Создание инстанса модели Upload
         */
        $this->upload = new Upload();


    }


    /**
     * Загрузка файла
     * @param UploadedFile $file
     * @param int|null $parentId
     * @param $owner
     * @return void
     */
    public function upload(UploadedFile $file, int $parentId = null, $owner): void
    {
        if ($file->isValid())
        {
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            if ($file->storeAs('public/uploads', $fileName, 'local'))
            {
                $this->upload->create([
                    'type' => $this->getUploadedFileType($file),
                    'group_id' => $parentId,
                    'client_file_name' => $file->getClientOriginalName(),
                    'original_file_name' => $fileName,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'owner' => $owner
                ]);
            }

        }
    }

    public function uploadList(int $groupId = null, array $filter = []): Collection
    {
        $query = $this->upload

            ->orderBy('created_at', 'desc')
            ->orderBy('type');

        if (isset($filter['mime']))
        {
            switch ($filter['mime'])
            {
                case 'image':
                    $query->where('mime', 'like', "image/%");
                    break;

                case 'pdf':
                    $query->where('mime', 'like', "%pdf%");
                    break;

                case 'excel':
                    $query->where('mime', 'like', "%spreadsheetml%");
                    break;

                case 'word':
                    $query->where('mime', 'like', "%msword%");
                    $query->orWhere('mime', 'like', "%wordprocessingml%");
                    break;

                case 'zip':
                    $query->where('mime', "application/zip");
                    $query->orWhere('mime', "application/x-rar");
                    break;

                case 'video':
                    $query->where('mime', 'like',  "%video/%");
                    break;
            }
        } else {
            $query->where('group_id', $groupId);
        }


        return $query->get();
    }

    public function uploadById(int $id): Upload
    {
        return $this->upload->find($id);
    }

    public function uploadDelete(int $id): void
    {
        $upload = $this->upload->find($id);

        if ($upload && $path = file_exists(storage_path('app/public/uploads/' . $upload->getOriginal('original_file_name'))))
        {
            @unlink($path);
        }

        $upload->delete();
    }

    private function getUploadedFileType(UploadedFile $file)
    {
        $rules = ['file' => 'image'];
        $validator = Validator::make(['file' => $file], $rules);

        return ($validator->fails()) ? 'file' : 'image';
    }

    public function folderById(int $id): Category
    {
        return $this->category->find($id);
    }


    /**
     * Создание каталога (категории)
     * @param string $name
     * @param int|null $parentId
     * @return Category
     */
    public function folderStore(string $name, int $parentId = null): Category
    {
        return $this->category->create(
            [
                'owner' => $this->categoryOwner,
                'name' => $name,
                'slug' => uniqid(),
                'parent_id' => $parentId
            ]
        );
    }

    /**
     * Переименование каталога
     * @param int $id
     * @param string $name
     * @return Category
     */
    public function folderRename(int $id, string $name): Category
    {
        $folder = $this->category->find($id);
        $folder->name = $name;
        $folder->save();

        return $folder;
    }

    /**
     * Список каталогов внутри конкретного каталога
     * Чтобы получить корневые каталоги, нужно передать null в параметре
     * @param int|null $parentId
     * @return Collection
     */
    public function folderList(int $parentId = null): ?Collection
    {
        return $this->category
            ->where('owner', $this->categoryOwner)
            ->where('parent_id', $parentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Удаление каталога.
     * Все файлы привязанные к каталогу будут отображены в корне
     * @param int $id
     */
    public function folderDelete(int $id): void
    {
        $this->upload->where('group_id', $id)->update(['group_id' => null]);
        $this->category->destroy($id);
    }







}
