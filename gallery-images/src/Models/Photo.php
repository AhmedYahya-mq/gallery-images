<?php

namespace Ahmed\GalleryImages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class Photo extends Model
{
    use HasFactory;

    // قواعد التحقق
    public const MAX_SIZE = 5_000_000; // ~5MB
    public const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/avif',
    ];

    protected $table = 'photos';
    protected $fillable = [
        'name',
        'path',
        'size',
        'type',
        'extension',
        'width',
        'height',
    ];

    protected $casts = [
        'size' => 'int',
        'width' => 'int',
        'height' => 'int',
    ];

    protected $appends = ['url'];

    /**
     * Get the default disk name for storing photos.
     *
     * @return string اسم الـ disk الافتراضي من إعدادات Laravel
     */
    protected static function defaultDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * Get the absolute URL for the photo file from storage.
     *
     * @return string رابط الصورة على التخزين (Storage::url)
     */
    public function getUrlAttribute(): string
    {
        return asset(Storage::disk(static::defaultDisk())->url($this->path));
    }

    /**
     * Insert multiple photos at once and associate them with any model via the pivot table.
     *
     * @param iterable<\Illuminate\Http\UploadedFile> $files مجموعة ملفات الصور المرفوعة
     * @param array $options خيارات إضافية مثل disk والدليل
     * @return \Illuminate\Support\Collection<Photo> مجموعة الصور التي تم إدراجها أو جلبها
     *
     * ملاحظة: يتحقق من وجود الصور مسبقًا عبر المسار ويمنع التكرار.
     */
    public static function insertManyFor(iterable $files, array $options = [])
    {
        $rows = self::prepareRows($files, $options);
        if (empty($rows)) {
            return collect();
        }

        // تحقق من وجود الصور مسبقًا عبر المسار
        $existingPaths = self::whereIn('path', array_column($rows, 'path'))->pluck('path')->all();
        $newRows = array_filter($rows, fn($row) => !in_array($row['path'], $existingPaths, true));

        $now = now();
        foreach ($newRows as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        if (!empty($newRows)) {
            DB::table((new self)->getTable())->insert($newRows);
        }

        $allPaths = array_column($rows, 'path');
        $photos = self::whereIn('path', $allPaths)->get();

        return $photos;
    }

    /**
     * Prepare rows for photo insertion: validate files, extract dimensions, and store if not exists.
     *
     * @param iterable<\Illuminate\Http\UploadedFile> $photos مجموعة ملفات الصور
     * @param array $options خيارات مثل disk والدليل والحجم الأقصى وأنواع الـ mime المسموحة
     * @return array[] مصفوفة associative لكل صورة تحتوي بياناتها
     *
     * ملاحظة: يخزن الصورة على القرص إذا لم تكن موجودة ويحسب الأبعاد.
     */
    public static function prepareRows(iterable $photos, array $options = []): array
    {
        $disk = $options['disk'] ?? 'public';
        $directory = trim($options['directory'] ?? 'photos', '/');
        $maxSize = $options['max_size'] ?? self::MAX_SIZE;
        $allowedMimes = $options['allowed_mimes'] ?? self::ALLOWED_MIMES;

        $year = date('Y');
        $month = date('m');
        $targetDir = "{$directory}/{$year}/{$month}";

        $rows = [];

        foreach ($photos as $photo) {
            if (!$photo instanceof UploadedFile) {
                continue;
            }

            $size = (int) $photo->getSize();
            $mime = (string) $photo->getClientMimeType();

            if ($size <= 0 || $size > $maxSize) {
                continue;
            }
            if (!in_array($mime, $allowedMimes, true)) {
                continue;
            }

            // توليد اسم فريد بناءً على محتوى الملف لمنع التكرار
            $hash = md5_file($photo->getRealPath());
            $ext = strtolower($photo->getClientOriginalExtension() ?: $photo->extension());
            $storedPath = "{$targetDir}/{$hash}.{$ext}";

            // إذا كان الملف غير موجود على القرص، خزنه
            if (!Storage::disk($disk)->exists($storedPath)) {
                Storage::disk($disk)->put($storedPath, file_get_contents($photo->getRealPath()));
            }

            [$width, $height] = self::getImageDimensions($photo);

            $rows[] = [
                'name' => $photo->getClientOriginalName(),
                'path' => $storedPath,
                'size' => $size,
                'type' => $mime,
                'extension' => $ext,
                'width' => (int) $width,
                'height' => (int) $height,
            ];
        }

        return $rows;
    }

    /**
     * Get image dimensions (width, height) for a given file.
     *
     * @param \Illuminate\Http\UploadedFile $file ملف الصورة
     * @return array [width:int, height:int] أبعاد الصورة
     *
     * ملاحظة: يدعم SVG عبر دالة منفصلة.
     */
    protected static function getImageDimensions(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $mime = (string) $file->getClientMimeType();

        if ($ext === 'svg' || $mime === 'image/svg+xml') {
            return self::getSvgDimensions($file->getRealPath()) ?? [0, 0];
        }

        $info = @getimagesize($file->getRealPath());
        if (is_array($info) && isset($info[0], $info[1])) {
            return [(int) $info[0], (int) $info[1]];
        }

        return [0, 0];
    }

    /**
     * Deprecated: Prefer HasPhotos::addPhotos() which associates rows and uses one DB insert.
     *
     * Create photo records from uploaded files.
     *
     * @param iterable<\Illuminate\Http\UploadedFile> $photos مجموعة ملفات الصور
     * @param array $options خيارات إضافية
     * @return \Illuminate\Support\Collection<Photo> مجموعة الصور التي تم إنشاؤها
     */
    public static function createPhotos($photos, array $options = [])
    {
        $rows = self::prepareRows($photos, $options);
        if (empty($rows)) {
            return collect();
        }

        $now = now();
        foreach ($rows as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        DB::table((new self)->getTable())->insert($rows);

        $paths = array_column($rows, 'path');

        return self::whereIn('path', $paths)->recent()->get();
    }

    /**
     * Get SVG image dimensions from file path.
     *
     * @param string $path مسار ملف الـ SVG
     * @return array<int>|null [width:int, height:int] أو null إذا فشل التحليل
     *
     * ملاحظة: يحاول استخراج الأبعاد من خصائص width/height أو من viewBox.
     */
    public static function getSvgDimensions($path)
    {
        $svg = @simplexml_load_file($path);
        if ($svg === false) {
            return null;
        }

        $attributes = $svg->attributes();

        $extract = function ($value) {
            if ($value === null) return null;
            $v = trim((string) $value);
            return (float) preg_replace('/[a-z%]+$/i', '', $v);
        };

        $width = $extract($attributes['width'] ?? null);
        $height = $extract($attributes['height'] ?? null);

        if (!$width || !$height) {
            // Fallback to viewBox (minX minY width height)
            $viewBox = (string) ($attributes['viewBox'] ?? '');
            if ($viewBox !== '') {
                $parts = preg_split('/[\s,]+/', trim($viewBox));
                if (count($parts) === 4) {
                    $width = (float) $parts[2];
                    $height = (float) $parts[3];
                }
            }
        }

        return [$width ? (int) round($width) : 0, $height ? (int) round($height) : 0];
    }

    /**
     * Delete a single photo from the database and storage.
     *
     * @return bool true إذا تم الحذف بنجاح، false خلاف ذلك
     *
     * ملاحظة: يحذف الملف من التخزين ثم يحذف السطر من قاعدة البيانات.
     */
    public function deletePhoto(): bool
    {
        // حذف الملف من التخزين
        Storage::disk(static::defaultDisk())->delete($this->path);
        // حذف السطر من قاعدة البيانات
        return $this->delete();
    }

    /**
     * Delete a group of photos by their IDs from the database and storage.
     *
     * @param array<int> $ids مصفوفة معرفات الصور
     * @return int عدد الصور التي تم حذفها فعليًا
     *
     * ملاحظة: يحذف كل صورة من التخزين وقاعدة البيانات.
     */
    public static function deletePhotosByIds(array $ids): int
    {
        $count = 0;
        // جلب الصور المطلوبة
        $photos = self::whereIn('id', $ids)->get();
        foreach ($photos as $photo) {
            if ($photo->deletePhoto()) {
                $count++;
            }
        }
        return $count;
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
