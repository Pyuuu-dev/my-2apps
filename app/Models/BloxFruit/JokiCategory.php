<?php

namespace App\Models\BloxFruit;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;

class JokiCategory extends Model
{
    use HasSlug;

    protected string $slugSource = 'label';

    protected $fillable = ['key', 'label', 'icon', 'urutan', 'aktif', 'slug'];

    protected $casts = [
        'aktif'  => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Helper untuk dapat array kategori dengan format:
     * [ 'level' => ['label' => 'Joki Level', 'icon' => '⚔️'], ... ]
     *
     * Diurutkan berdasarkan kolom urutan (asc).
     */
    public static function labels(bool $onlyActive = true): array
    {
        $q = static::query()->orderBy('urutan')->orderBy('id');
        if ($onlyActive) {
            $q->where('aktif', true);
        }

        return $q->get()
            ->mapWithKeys(fn($c) => [
                $c->key => [
                    'label' => $c->label,
                    'icon'  => $c->icon ?: '📝',
                ],
            ])
            ->toArray();
    }

    /**
     * Versi flat untuk dropdown (form-select):
     * [ 'level' => 'Joki Level', ... ]
     */
    public static function selectOptions(bool $onlyActive = true): array
    {
        $q = static::query()->orderBy('urutan')->orderBy('id');
        if ($onlyActive) {
            $q->where('aktif', true);
        }

        return $q->pluck('label', 'key')->toArray();
    }

    /**
     * Versi flat label saja (untuk Rekap):
     * [ 'level' => 'Joki Level', ... ]
     */
    public static function flatLabels(bool $onlyActive = false): array
    {
        return static::selectOptions($onlyActive);
    }

    public function services()
    {
        return $this->hasMany(JokiService::class, 'kategori', 'key');
    }
}
