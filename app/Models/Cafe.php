<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cafe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo_url'
    ];

    public function diaries(): HasMany
    {
        return $this->hasMany(Diary::class);
    }

    static function schema()
    {
        Schema::create('cafes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo_url')->nullable();

            $table->timestamps();
        });
    }
}
