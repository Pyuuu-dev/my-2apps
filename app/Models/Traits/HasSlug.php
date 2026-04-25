<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });

        static::updating(function ($model) {
            $sourceCol = $model->slugSource ?? null;
            if ($sourceCol && $model->isDirty($sourceCol)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    protected function generateUniqueSlug(): string
    {
        $sourceCol = $this->slugSource ?? null;
        $base = $sourceCol ? Str::slug($this->$sourceCol ?? '') : Str::random(8);
        if (empty($base)) $base = 'item';

        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
