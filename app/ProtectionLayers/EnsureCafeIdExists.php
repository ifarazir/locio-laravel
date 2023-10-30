<?php
namespace App\ProtectionLayers;
use App\Http\Responses\Admin\CafeResponse;
use App\Services\Cafe\CafeService;
use Imanghafoori\HeyMan\Facades\HeyMan;
class EnsureCafeIdExists
{
    public static function install()
    {
        HeyMan::onCheckPoint('EnsureCafeIdExists')
            ->thisMethodShouldAllow([static::class, 'check'])
            ->otherwise()
            ->afterCalling([self::class, 'react'])
            ->weRespondFrom([CafeResponse::class, 'invalidCafeId']);
    }
    public static function check(): bool
    {
        $id = request()->route()->parameter('cafe');
        return is_numeric($id) && CafeService::new()->findByIdWithRelation((int)$id);
    }
    public static function react()
    {
        \Log::alert('Tried to access a non-existing cafe!', [
            'user_id' => auth()->id(),
            'route' => request()->route()->getName(),
            'cafe_id' => request()->route()->parameter('cafe')
        ]);
    }
}
