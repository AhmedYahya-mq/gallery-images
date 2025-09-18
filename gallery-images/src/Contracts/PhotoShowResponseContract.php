<?php
namespace Ahmed\GalleryImages\Contracts;

use Illuminate\Contracts\Support\Responsable;
interface PhotoShowResponseContract extends Responsable
{
    public function __construct($photos);
    /**
     * Handle the response for showing photos.
     *
     * @param mixed $response كائن الاستجابة (PhotoCollection أو PhotoResource أو رسالة خطأ)
     * @return mixed استجابة مناسبة لعرض الصور
     */
    public function toResponse($response);
}

