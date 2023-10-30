<?php
namespace App\Services\File;
use App\Models\File;
use App\Services\ModelService;
use Imanghafoori\Helpers\Nullable;
class FileService extends ModelService
{
    public function __construct(File $file)
    {
        $this->setModel($file);
    }
    public static function new()
    {
        return static::make(new File());
    }
    public static function make(File $file)
    {
        return new static($file);
    }
    public function create(array $data = []): ?Nullable
    {
        return parent::create($data);
    }
    public function update($data = []): Nullable
    {
        return parent::update($data);
    }
    public function delete(): ?Nullable
    {
        return parent::delete();
    }
}
