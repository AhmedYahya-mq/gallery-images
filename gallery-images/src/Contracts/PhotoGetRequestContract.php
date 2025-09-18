<?php
namespace Ahmed\GalleryImages\Contracts;

interface PhotoGetRequestContract
{
    /**
     * Get the photo IDs from the request.
     *
     * @return array<int> مصفوفة معرفات الصور المطلوبة
     */
    public function photoIds(): array;
}
