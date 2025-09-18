<?php

namespace Ahmed\GalleryImages\Contracts;

use Illuminate\Contracts\Support\Responsable;

interface PhotoDeleteResponseContract extends Responsable
{
    /**
     * Create a new PhotoDeleteResponseContract instance.
     *
     * @param string $message رسالة الحذف
     * @param int $deleted عدد الصور المحذوفة
     * @param array<int> $ids معرفات الصور المحذوفة
     */
    public function __construct($message, $deleted, $ids);

    /**
     * Generate a response for photo deletion.
     *
     * @param \Illuminate\Http\Request $request كائن الطلب الحالي
     * @return \Illuminate\Http\JsonResponse استجابة JSON تحتوي معلومات الحذف
     */
    public function toResponse($request);
}
