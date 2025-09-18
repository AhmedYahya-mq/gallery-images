<?php

namespace Ahmed\GalleryImages\Http\Responses;

use Ahmed\GalleryImages\Contracts\PhotoCollectionContract;
use Ahmed\GalleryImages\Contracts\PhotoResourceContract;
use Ahmed\GalleryImages\Contracts\PhotoShowResponseContract;

class PhotoShowResponse implements PhotoShowResponseContract
{
    protected $photos;

    /**
     * Create a new PhotoShowResponse instance.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $photos مجموعة الصور المطلوبة
     */
    public function __construct($photos)
    {
        $this->photos = $photos;
    }

    /**
     * Handle the response for showing photos.
     *
     * @param mixed $response كائن الاستجابة (يتم تجاهله هنا)
     * @return mixed استجابة مناسبة: PhotoCollection أو PhotoResource أو رسالة خطأ
     *
     * ملاحظة: يختار نوع الاستجابة حسب عدد الصور.
     */
    public function toResponse($response)
    {
        if (count($this->photos) > 1) {
            $response = app(PhotoCollectionContract::class, ['resource' => $this->photos]);
        } elseif ($this->photos->count() === 1) {
            $response = app(PhotoResourceContract::class, ['resource' => $this->photos->first()]);
        } else {
            $response = response()->json(['message' => __('No photos found')], 404);
        }

        return $response;
    }
}
