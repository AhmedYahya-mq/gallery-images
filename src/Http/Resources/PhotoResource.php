<?php

namespace Ahmed\GalleryImages\Http\Resources;

use Ahmed\GalleryImages\Contracts\PhotoResourceContract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource implements PhotoResourceContract
{
    /**
     * Transform the photo resource into an array for API response.
     *
     * @param \Illuminate\Http\Request $request كائن الطلب الحالي
     * @return array<string, mixed> مصفوفة بيانات الصورة
     *
     * ملاحظة: يعرض معلومات الصورة الأساسية، المسار النسبي، والرابط المطلق من Storage.
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            // keep relative path from DB
            "path" => $this->path,
            // absolute URL from model accessor (Storage::url)
            "url" => $this->url,
            "size" => $this->size,
            "type" => $this->type,
            "extension" => $this->extension,
            "width" => $this->width,
            "height" => $this->height,
            "selected" => false,
            "created_at" => $this->created_at,
        ];
    }
}
