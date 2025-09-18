<?php
namespace Ahmed\GalleryImages\Contracts;

/**
 * Interface PhotoResourceContract
 * This interface defines the contract for photo resources.
 * It includes a method to transform the resource into an array.
 * @package Ahmed\GalleryImages\Contracts
 */
interface PhotoResourceContract
{
    /**
     * Transform the resource into an array for API response.
     *
     * @param mixed $resource كائن الصورة أو النموذج
     * @return array<string, mixed> مصفوفة بيانات الصورة
     */
    public function toArray($resource): array;
}