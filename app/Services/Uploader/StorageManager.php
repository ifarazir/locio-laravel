<?php
namespace App\Services\Uploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class StorageManager
{
    public function putFile(string $name, UploadedFile $file, string $format)
    {
        return Storage::disk('public')->putFileAs($format, $file, $name);
    }
    public function getAbsolutePathOf(string $name, string $format)
    {
        return Storage::path($this->directoryPrefix($format, $name));
    }
    public function isFileExists(string $name, string $format)
    {
        return Storage::exists($this->directoryPrefix($format, $name));
    }
    public function getFile(string $name, string $format)
    {
        return Storage::download($this->directoryPrefix($format, $name));
    }
    public function deleteFile(string $name, string $format)
    {
        return Storage::delete($this->directoryPrefix($format, $name));
    }
    private function directoryPrefix($format, $name)
    {
        return $format . DIRECTORY_SEPARATOR . $name;
    }
}
