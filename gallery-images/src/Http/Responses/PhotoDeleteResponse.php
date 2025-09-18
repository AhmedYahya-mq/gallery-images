<?php

namespace Ahmed\GalleryImages\Http\Responses;

use Ahmed\GalleryImages\Contracts\PhotoDeleteResponseContract;

class PhotoDeleteResponse implements PhotoDeleteResponseContract
{
    protected $deleted;
    protected $ids;
    protected $message;

    /**
     * Create a new PhotoDeleteResponse instance.
     *
     * @param string $messages رسالة الحذف
     * @param int $deleted عدد الصور المحذوفة
     * @param array<int> $ids معرفات الصور المحذوفة
     */
    public function __construct($messages, $deleted, $ids)
    {
        $this->message = $messages;
        $this->deleted = $deleted;
        $this->ids = $ids;
    }

    /**
     * Generate a JSON response for photo deletion.
     *
     * @param \Illuminate\Http\Request $request كائن الطلب الحالي
     * @return \Illuminate\Http\JsonResponse استجابة JSON تحتوي معلومات الحذف
     */
    public function toResponse($request)
    {
        return response()->json([
            'message' => $this->message,
            'deleted' => $this->deleted,
            'ids' => $this->ids,
            'success' => $this->deleted > 0,
        ]);
    }
}
