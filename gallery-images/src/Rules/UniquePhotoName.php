<?php

namespace Ahmed\GalleryImages\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UniquePhotoName implements Rule
{
    public function passes($attribute, $value): bool
    {
        // إذا كان الملف مرفوع (UploadedFile)
        if ($value instanceof UploadedFile) {
            $filename = $value->getClientOriginalName();
            // تحقق من عدم وجود اسم الملف في جدول الصور
            return !DB::table('photos')->where('name', $filename)->exists();
        }
        return false;
    }

    public function message(): string
    {
        return 'اسم الصورة مستخدم من قبل، يرجى اختيار اسم آخر.';
    }
}
