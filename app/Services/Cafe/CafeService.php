<?php
namespace App\Services\Cafe;
use App\Models\Cafe;
use App\Services\ModelService;
use Imanghafoori\Helpers\Nullable;
class CafeService extends ModelService
{
    public function __construct(Cafe $cafe)
    {
        $this->setModel($cafe);
    }
    public static function new()
    {
        return static::make(new Cafe());
    }
    public static function make(Cafe $cafe)
    {
        return new static($cafe);
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
