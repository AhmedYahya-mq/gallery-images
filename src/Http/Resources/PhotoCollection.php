<?php

namespace Ahmed\GalleryImages\Http\Resources;

use Ahmed\GalleryImages\Contracts\PhotoCollectionContract;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
class PhotoCollection extends ResourceCollection implements PhotoCollectionContract
{

    /**
     * Create a new resource collection instance.
     *
     * @param mixed $resource مجموعة الصور أو paginator
     * @return static كائن PhotoCollection جديد
     *
     * ملاحظة: يمكن تمرير مجموعة أو paginator.
     */
    public function __invoke($resource)
    {
        return new self($resource);
    }

    /**
     * Transform the resource collection into an array for API response.
     *
     * @param \Illuminate\Http\Request $request كائن الطلب الحالي
     * @return array<string, mixed> مصفوفة بيانات المجموعة
     *
     * ملاحظة: العناصر مغلفة تلقائيًا بـ PhotoResource.
     */
    public function toArray(Request $request): array
    {
        // items will be PhotoResource due to $collects
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * Add additional meta information like pagination to the response.
     *
     * @param \Illuminate\Http\Request $request كائن الطلب الحالي
     * @return array<string, mixed> معلومات إضافية مثل الصفحات
     */
    public function with(Request $request): array
    {
        return $this->paginationInfo();
    }

    /**
     * Get pagination info if the resource is paginated.
     *
     * @return array<string, mixed> معلومات عن الصفحات أو مصفوفة فارغة
     */
    protected function paginationInfo(): array
    {
        if (
            $this->resource instanceof \Illuminate\Pagination\Paginator ||
            $this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator
        ) {
            return [
                'has_more' => $this->resource->hasMorePages(),
                'next_page' => $this->resource->hasMorePages()
                    ? $this->resource->currentPage() + 1
                    : $this->resource->currentPage(),
            ];
        }

        return [];
    }
}
