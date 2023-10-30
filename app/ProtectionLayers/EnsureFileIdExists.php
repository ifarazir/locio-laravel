<?php

namespace App\ProtectionLayers;

use App\Http\Responses\Admin\FileResponse;
use App\Services\File\FileService;
use Imanghafoori\HeyMan\Facades\HeyMan;

class EnsureFileIdExists
{
    public static function install()
    {
        HeyMan::onCheckPoint('EnsureFileIdExists')
            ->thisMethodShouldAllow([static::class, 'fileIdExists'])
            ->otherwise()
            ->afterCalling([self::class, 'react'])
            ->weRespondFrom([FileResponse::class, 'invalidFileId']);
    }

    public static function fileIdExists(): bool
    {
        $id = request()->route()->parameter('file');
        return is_numeric($id) && FileService::new()->findByIdWithRelation((int)$id);
    }

    public static function react()
    {
        \Log::alert('Tried to access a non-existing file!', [
            'user_id' => auth()->id(),
            'route' => request()->route()->getName(),
            'file_id' => request()->route()->parameter('file')
        ]);
    }
}
