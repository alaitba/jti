<?php


namespace App\Services\MediaService;

use Exception;

// facades
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

// models
use App\Models\Media;


/**
 * Class MediaService
 * @package App\Services\MediaService
 */
class MediaService
{
    /**
     * Путь загрузки изображений отностиельно storage/app/public
     */
    const UPLOAD_PATH = "media";


    /**
     * Загрузка изображения
     * @param UploadedFile $image
     * @param $imageableType
     * @param $imageableId
     * @param array $conversions
     * @return mixed
     * @throws Exception
     */
    public function upload(UploadedFile $image, string $imageableType, int $imageableId, array $conversions = []): Media
    {

        /**
         * Проверяем есть ли в конфиг файле переданные конверсии
         */
        if (count($conversions)) {
            foreach ($conversions as $conversion) {
                if (!in_array($conversion, config('media.conversions'))) {
                    throw new Exception('Conversion ' . $conversion . ' not present in config');
                }
            }
        }

        /**
         * Если изображение не валидно выбросим исклчение
         */
        if (!$image->isValid()) {
            throw new Exception('Argument $image is not valid media resource');
        }

        if(!Storage::exists('app/public/' . self::UPLOAD_PATH)){
            Storage::disk('local')->makeDirectory('public/' . self::UPLOAD_PATH);
        }
        // определяем путь загрузки изображений
        $uploadPath = storage_path('app/public/' . self::UPLOAD_PATH);


        $clientName = $image->getClientOriginalName();
        $originalFileName = uniqid() . '.' . $image->guessClientExtension();

        // создаем инстанс Intervention Image
        $img = Image::make($image);

        // сохраняем оригинальное изображение
        $img->save($uploadPath . '/' . $originalFileName,90);

        $size = $img->filesize();
        $mime = $img->mime();

        // определяем конверсии. Берем из параметра или из конфига
        $conversions = (count($conversions)) ? $conversions : config('media.conversions');
        $converted = [];

        // ресайзим конверсии
        foreach ($conversions as $conversion => $size) {
            $img = Image::make($image);

            $img->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $convertedName = $conversion . '_' . $originalFileName;


            $img->save($uploadPath . '/' . $convertedName,90);

            $converted[$conversion] = [
                'name' => $convertedName,
                'width' => $img->width(),
                'height' => $img->height(),
                'mime' => $img->mime()
            ];
        }

        $media = new Media();

        return $media->create([
            'imageable_type' => $imageableType,
            'imageable_id' => $imageableId,
            'client_file_name' => $clientName,
            'original_file_name' => $originalFileName,
            'conversions' => $converted,
            'size' => $size,
            'mime' => $mime
        ]);
    }

    /**
     * Загрузка изображения без сохранения в базу
     * @param UploadedFile $image
     * @return mixed
     * @throws Exception
     */
    public function uploadWithoutRecord(UploadedFile $image): array
    {
        /**
         * Если изображение не валидно выбросим исклчение
         */
        if (!$image->isValid()) {
            throw new Exception('Argument $image is not valid media resource');
        }

        if(!Storage::exists('app/public/' . self::UPLOAD_PATH)){
            Storage::disk('local')->makeDirectory('public/' . self::UPLOAD_PATH);
        }
        // определяем путь загрузки изображений
        $uploadPath = storage_path('app/public/' . self::UPLOAD_PATH);


        $clientName = $image->getClientOriginalName();
        $originalFileName = uniqid() . '.' . $image->guessClientExtension();

        // создаем инстанс Intervention Image
        $img = Image::make($image);

        // сохраняем оригинальное изображение
        $img->save($uploadPath . '/' . $originalFileName,90);

        $size = $img->filesize();
        $mime = $img->mime();

        return [
            'client_file_name' => $clientName,
            'original_file_name' => $originalFileName,
            'size' => $size,
            'mime' => $mime
        ];
    }


    /**
     * Установка изображения главным
     * @param $mediaId
     * @param $imageableType
     * @param $imageableId
     */
    public function setMainById(int $mediaId, string $imageableType, int $imageableId): void
    {

        // сначала сделаем уже главное изображение не главным
        // если такое есть
        Media::where('imageable_type', $imageableType)
            ->where('imageable_id', $imageableId)
            ->update(['main_image' => 0]);


        $media = Media::find($mediaId);
        $media->main_image = 1;
        $media->save();
    }

    /**
     * Удаление изображение для сущности
     * @param $imageableType
     * @param $imageableId
     * @throws Exception
     */
    public function deleteForModel(string $imageableType, int $imageableId): void
    {
        $images = Media::where('imageable_type', $imageableType)->where('imageable_id', $imageableId)->get();

        foreach ($images as $image) {
            $this->delete($image);
        }
    }

    /**
     * Удаление изображения по его айди
     * @param $mediaId
     * @throws Exception
     */
    public function deleteById(int $mediaId): void
    {
        $media = Media::find($mediaId);
        $this->delete($media);
    }

    /**
     * Приватный метод для удаления изображений для модели
     * @param Media $media
     * @throws Exception
     */
    private function delete(Media $media): void
    {

        /**
         * Если удаляемое изображение является главным для сущности
         * то найдем первое попавшееся не главное и сделаем его главным
         */
        if ($media->main_image) {
            $mainMedia = Media::where('imageable_type', $media->imageable_type)
                ->where('imageable_id', $media->imageable_id)
                ->where('id', '!=', $media->id)
                ->where('main_image', 0)->first();

            if ($mainMedia) {
                $mainMedia->main_image = 1;
                $mainMedia->save();
            }
        }

        $this->deleteFile($media->getOriginal('original_file_name'));
        $conversions = $media->conversions;

        foreach ($conversions as $k => $conversion) {
            $this->deleteFile($conversion['name']);
        }

        $media->delete();

    }

    /**
     * Удаление файла
     * @param string $name
     */
    private function deleteFile(string $name): void
    {
        $path = storage_path('app/public/' . self::UPLOAD_PATH) . '/' . $name;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * @param int $mediaId
     * @return mixed
     */
    public function getMedia(int $mediaId)
    {
        return Media::find($mediaId);
    }

    /**
     * @param string $imageableType
     * @param int $imageableId
     * @return mixed
     */
    public function getMediaForType(string $imageableType, int $imageableId)
    {
        return Media::where('imageable_type', $imageableType)->where('imageable_id', $imageableId)->get();
    }


    /**
     * @param string $path
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function cropImage(string $path, array $params): string
    {

        if (!file_exists($path))
        {
            throw new Exception('File ' . $path . ' not found');
        }

        if (!isset($params['dataWidth']) || !isset($params['dataHeight']) ||  !isset($params['dataX']) || !isset($params['dataY']))
        {
            throw new Exception('Invalid params');
        }

        $image = Image::make($path);

        if (isset($params['dataScaleY']) && $params['dataScaleY'] < 0)
        {
            $image->flip('v');
        }


        if (isset($params['dataScaleX']) && $params['dataScaleX'] < 0)
        {
            $image->flip('h');
        }

        $image->crop($params['dataWidth'], $params['dataHeight'], $params['dataX'], $params['dataY']);




        $image->save($path,90);

        return $path;
    }

    /**
     * @param string $src
     * @param string $target
     * @param int $width
     * @param int $height
     */
    public function resize(string $src, string $target, int $width, int $height): void
    {
        $image = Image::make($src);

        $image->resize($width, $height, function ($constraint)
        {
            $constraint->aspectRatio();
        });

        $image->save($target, 90);


    }

}

