<?php

namespace Ahmed\GalleryImages;

use Ahmed\GalleryImages\Models\Photo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait HasPhotos
{
    /**
     * Polymorphic many-to-many relation to photos via photoables table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany علاقة الصور المرتبطة بالموديل
     */
    public function photos(): MorphToMany
    {
        return $this->morphToMany(
            Photo::class,
            'photoable',
            'photoables',
            'photoable_id',
            'photo_id'
        )->withTimestamps();
    }

    /**
     * Add a single photo and associate it with the model.
     *
     * @param \Illuminate\Http\UploadedFile $file ملف صورة واحد
     * @param array $options خيارات إضافية مثل disk والدليل
     * @return \Ahmed\GalleryImages\Models\Photo|null الصورة التي تم إضافتها أو null إذا فشل
     *
     * ملاحظة: يستخدم addPhotos داخليًا ويعيد أول عنصر.
     */
    public function addPhoto($file, array $options = [])
    {
        return $this->addPhotos([$file], $options)->first();
    }

    /**
     * Add multiple photos and associate them with the model.
     *
     * @param iterable<\Illuminate\Http\UploadedFile> $files مجموعة ملفات الصور
     * @param array $options خيارات إضافية
     * @return \Illuminate\Support\Collection<Photo> مجموعة الصور التي تم إضافتها
     *
     * ملاحظة: يربط الصور الجديدة بدون إزالة القديمة.
     */
    public function addPhotos(iterable $files, array $options = [])
    {
        $photos = Photo::insertManyFor($files, $options);
        $this->photos()->syncWithoutDetaching($photos->pluck('id')->all());
        return $photos;
    }

    /**
     * Sync photos: remove all old photos and associate only the new ones.
     *
     * @param iterable<\Illuminate\Http\UploadedFile> $files مجموعة ملفات الصور الجديدة
     * @param array $options خيارات إضافية
     * @return \Illuminate\Support\Collection<Photo> مجموعة الصور المرتبطة بعد المزامنة
     *
     * ملاحظة: يحذف جميع العلاقات القديمة ويربط فقط الصور الجديدة.
     */
    public function syncPhotos(iterable $files, array $options = []): Collection
    {
        $photos = Photo::insertManyFor($files, $options);
        $this->photos()->sync($photos->pluck('id')->all());
        return $photos;
    }

    /**
     * Scope to eager load photos with the model.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query كائن الاستعلام
     * @return \Illuminate\Database\Eloquent\Builder الاستعلام مع تحميل الصور
     */
    public function scopeWithPhotos(Builder $query): Builder
    {
        return $query->with('photos');
    }

    /**
     * Get the disk name for storing photos, can be customized per model.
     *
     * @return string اسم الـ disk المستخدم
     */
    protected function photoDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * Get the directory name for storing photos, can be customized per model.
     *
     * @return string اسم الدليل المستخدم لتخزين الصور
     */
    protected function photoDirectory(): string
    {
        return 'photos';
    }

    /**
     * Synchronize photos with the model.
     * - يحذف الصور القديمة إذا موجودة ويربط الصور الجديدة.
     * - يقبل ID واحد أو مصفوفة من IDs.
     *
     * @param int|array $photoIds
     * @return void
     */
    public function syncPhotosById(int|array $photoIds): void
    {
        $ids = is_array($photoIds) ? $photoIds : [$photoIds];
        $this->photos()->sync($ids);
    }

    /**
     * Attach photos without removing existing ones.
     * - يقبل ID واحد أو مصفوفة من IDs.
     *
     * @param int|array $photoIds
     * @return void
     */
    public function attachPhotosById(int|array $photoIds): void
    {
        $ids = is_array($photoIds) ? $photoIds : [$photoIds];
        $this->photos()->syncWithoutDetaching($ids);
    }

    /**
     * Detach all photos from the model.
     *
     * @return void
     */
    public function clearPhotos(): void
    {
        $this->photos()->detach();
    }

    /**
     * Get all attached photo IDs as an array.
     *
     * @return array
     */
    public function photoIds(): array
    {
        return $this->photos()->pluck('photo_id')->toArray();
    }

    /**
     * Get the first photo attached to the model, or null.
     *
     * @return \Ahmed\GalleryImages\Models\Photo|null
     */
    public function firstPhoto(): ?Photo
    {
        return $this->photos()->first();
    }
}
