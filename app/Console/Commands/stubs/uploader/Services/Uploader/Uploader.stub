<?php

namespace App\Services\Uploader;

use App\Models\File;
use Illuminate\Http\Request;

class Uploader
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var StorageManager
     */
    private $storageManager;

    private $file;

    /**
     * @var FFMpegService
     */
    private $ffmpeg;


    public function __construct(Request $request, StorageManager $storageManager)
    {
        $this->request = $request;
        $this->storageManager = $storageManager;
        $this->file = $request->file;
    }


    public function upload()
    {
        $name = $this->file->getClientOriginalName();

        if ($this->isFileExists($name)) {
            $name = $this->addRandomIntToFileName($name);
        }
        $this->putFileIntoStorage($name);

        return $this->saveFileIntoDatabase($name);
    }

    private function addRandomIntToFileName($name)
    {
        $randName = mt_rand(10000000, 99999999) . $name;

        if ($this->isFileExists($randName)) {
            $randName = $this->addRandomIntToFileName($name);
        }

        return $randName;
    }

    private function saveFileIntoDatabase($name)
    {
        $file = new File([
            'name' => $name,
            'size' => $this->file->getSize(),
            'type' => $this->getType()
        ]);

        $file->save();

        return $file;
    }


    private function putFileIntoStorage($name)
    {
        $this->storageManager->putFile($name, $this->file, $this->getType());
    }

    private function getType()
    {
        return [
            'image/jpeg' => 'image',
            'video/mp4' => 'video',
            'video/quicktime' => 'video',
            'image/svg+xml' => 'image',
            'image/png' => 'image',
            'application/zip' => 'archive',
            'audio/webm' => 'audio'
        ][$this->file->getClientMimeType()];
    }

    private function isFileExists($name)
    {
        return $this->storageManager->isFileExists($name, $this->getType());
    }
}
