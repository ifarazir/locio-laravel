<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Diary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'file_id',
        'cafe_id',
        'ip'
    ];

    protected $appends = ['file_url', 'likes_count', 'is_like'];

    public function getLikesCountAttribute()
    {
        return \DB::table('diary_like')->where('diary_id', $this->id)->count();
    }

    public function getIsLikeAttribute()
    {
        return \DB::table('diary_like')->where('ip', request()->ip())->where('diary_id', $this->id)->exists();
    }

    public function getFileUrlAttribute()
    {
        return $this->file->url;
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(file::class);
    }

    static function schema()
    {
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('file_id');
            $table->foreign('file_id')->references('id')->on('files')->cascadeOnDelete();

            $table->unsignedBigInteger('cafe_id');
            $table->foreign('cafe_id')->references('id')->on('cafes')->cascadeOnDelete();

            $table->string('ip')->nullable();

            $table->timestamps();
        });
    }
}
