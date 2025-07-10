<?php

namespace MountainClans\LivewireTiptap\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use MountainClans\LivewireTiptap\Models\EditorMedia;

trait HasEditorMedia
{
    public function editorMedia(): MorphMany
    {
        return $this->morphMany(EditorMedia::class, 'model');
    }

    protected static function bootHasEditorMedia(): void
    {
        static::deleting(function ($model) {
            $model->removeTiptapImages();
        });
    }

    public function processUploadedImages(string $field): void
    {
        if (method_exists($this, 'isTranslatableAttribute') && $this->isTranslatableAttribute($field)) {
            // Для переводимых полей используем getTranslations()
            $fieldValue = $this->getTranslations($field);
        } else {
            // Для обычных полей создаем массив с default ключом
            $rawValue = $this->getAttribute($field);
            $fieldValue = $rawValue ? ['default' => $rawValue] : [];
        }

        if (empty($fieldValue)) {
            return;
        }

        $currentImagePaths = $this->extractImagePaths($fieldValue);

        $existingMedia = $this->editorMedia()->get();
        $existingPaths = $existingMedia->pluck('path')->toArray();

        // Обрабатываем картинки из поля
        foreach ($currentImagePaths as $path) {
            if (!in_array($path, $existingPaths)) {
                // Ищем "висячую" запись EditorMedia без привязки к модели
                $orphanMedia = EditorMedia::where('path', $path)
                    ->whereNull('model_type')
                    ->whereNull('model_id')
                    ->first();

                if ($orphanMedia) {
                    // Привязываем существующую запись к текущей модели
                    $orphanMedia->update([
                        'model_type' => get_class($this),
                        'model_id' => $this->getKey(),
                        'field' => $field,
                    ]);
                } else {
                    // Создаем новую запись, если не найдена "висячая"
                    $this->editorMedia()->create([
                        'path' => $path,
                    ]);
                }
            }
        }

        $pathsToRemove = array_diff($existingPaths, $currentImagePaths);
        if (!empty($pathsToRemove)) {
            $this->editorMedia()
                ->whereIn('path', $pathsToRemove)
                ->get()
                ->each(function (EditorMedia $media) {
                    $media->delete(); // Файл удалится автоматически через модель
                });
        }
    }

    public function removeTiptapImages(): void
    {
        $this->editorMedia()->get()->each(function (EditorMedia $media) {
            $media->delete(); // Файл удалится автоматически через модель
        });
    }

    protected function extractImagePaths(array $fieldValue): array
    {
        $imagePaths = [];

        // Проходим по всем переводам (или по единственному 'default' ключу)
        foreach ($fieldValue as $content) {
            if (!is_string($content) || empty($content)) {
                continue;
            }

            // Извлекаем все src изображений
            preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $src) {
                    // Конвертируем полный URL в относительный путь
                    $relativePath = $this->convertUrlToRelativePath($src);
                    if ($relativePath && !in_array($relativePath, $imagePaths)) {
                        $imagePaths[] = $relativePath;
                    }
                }
            }
        }

        return $imagePaths;
    }

    /**
     * Конвертация URL изображения в относительный путь
     *
     * @param string $url URL изображения
     * @return string|null Относительный путь или null, если не удалось конвертировать
     */
    protected function convertUrlToRelativePath(string $url): ?string
    {
        // Удаляем домен и /storage/ префикс, если есть
        $url = preg_replace('#^https?://[^/]+#', '', $url);
        $url = ltrim($url, '/');

        // Если начинается с storage/, убираем этот префикс
        if (str_starts_with($url, 'storage/')) {
            $url = substr($url, 8);
        }

        // Проверяем, что это картинка из tiptap директории
        if (str_starts_with($url, 'tiptap/') && Storage::disk('public')->exists($url)) {
            return $url;
        }

        return null;
    }
}
