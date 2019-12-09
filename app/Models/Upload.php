<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'uploads';
    protected $fillable = [
        'owner',
        'type',
        'group_id',
        'client_file_name',
        'original_file_name',
        'size',
        'conversions',
        'meta',
        'mime',
    ];

    public function getMimeAttribute($value)
    {
        switch ($value)
        {
            case strpos($value, 'video'):
                return '<i class="fa fa-file-video-o"></i>';
                break;

            case strpos($value, 'image'):
                return '<i class="fa fa-file-image-o"></i>';
                break;

            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                return '<i class="fa fa-file-excel-o"></i>';
                break;

            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/msword':
                return '<i class="fa fa-file-word-o"></i>';
                break;

            case 'application/zip':
            case 'application/x-rar':
                return '<i class="fa fa-file-zip-o"></i>';
                break;

            case 'application/pdf':
                return '<i class="fa fa-file-pdf-o"></i>';
                break;

            case 'text/plain':
                return '<i class="fa fa-file-text-o"></i>';
                break;

            default:
                return '<i class="fa fa-file-code-o"></i>';
                break;
        }
    }

    public function getSizeAttribute($value)
    {
        $precision = 0;
        $unit = ['Б','КБ','МБ','ГБ','ТБ'];

        for($i = 0; $value >= 1024 && $i < count($unit)-1; $i++){
            $value /= 1024;
        }

        return round($value, $precision).' '.$unit[$i];
    }
}
