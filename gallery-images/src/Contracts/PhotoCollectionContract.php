<?php

namespace Ahmed\GalleryImages\Contracts;

/**
 * Interface PhotoCollectionContract
 * This interface defines the contract for photo resource collections.
 * It includes a method to create a new resource collection instance.
 * @package Ahmed\MyImagePackage\Contracts\Resources
 */
interface PhotoCollectionContract
{
    /**
     * Create a new resource collection instance.
     *
     * @param mixed $resource مجموعة الصور أو paginator
     * @return static كائن PhotoCollection جديد
     */
    public function __invoke($resource);
}
