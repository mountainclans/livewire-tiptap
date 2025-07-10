<?php

namespace MountainClans\LivewireTiptap\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class EditorMedia extends Model
{
    use HasUlids;

    protected $table = 'editor_media';

    protected $fillable = [
        'path',
        'model_type',
        'model_id',
        'field',
    ];

    protected static function booted(): void
    {
        static::deleting(function (EditorMedia $media) {
            if (Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }
        });
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::disk('public')->path($this->path);
    }
}
