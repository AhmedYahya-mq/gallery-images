<?php

namespace App\Http\Controllers;

use Ahmed\GalleryImages\Contracts\PhotoShowResponseContract;
use Ahmed\GalleryImages\Contracts\PhotoCollectionContract;
use Ahmed\GalleryImages\Contracts\PhotoGetRequestContract;
use Ahmed\GalleryImages\Contracts\PhotoRequestContract;
use Ahmed\GalleryImages\Contracts\PhotoDeleteResponseContract;
use Ahmed\GalleryImages\Models\Photo;

class PhotoController extends Controller
{
    /**
     * عرض قائمة الصور مع دعم التصفح (pagination).
     *
     * المدخلات: لا يوجد مدخلات مباشرة من المستخدم.
     * المخرجات: PhotoCollectionContract (Resource Collection) يحتوي على مجموعة الصور مع بيانات التصفح.
     *
     * ملاحظات:
     * - يتم استخدام الـ Contract لتهيئة الـ Resource Collection.
     * - الصور يتم جلبها من قاعدة البيانات مع تقسيم الصفحات (10 صور لكل صفحة).
     */
    public function index()
    {
        $photos = Photo::paginate(10);
        // استخدم الـ contract للـ collection
        return app(PhotoCollectionContract::class, ["resource" => $photos]);
    }

    /**
     * عرض مجموعة صور بناءً على معرفات محددة.
     *
     * المدخلات: PhotoGetRequestContract (Request) يحتوي على مجموعة معرفات الصور (IDs).
     * المخرجات: PhotoShowResponseContract (Response) يحتوي على مجموعة الصور المطلوبة.
     *
     * ملاحظات:
     * - يمكن تمرير معرفات متعددة دفعة واحدة.
     * - يتم استخدام الـ Contract لتهيئة الاستجابة.
     */
    public function show(PhotoGetRequestContract $request)
    {
        $ids = $request->photoIds();
        $photos = Photo::whereIn('id', $ids)->get();
        return app(PhotoShowResponseContract::class, ["photos" => $photos]);
    }

    /**
     * رفع صورة أو عدة صور جديدة إلى المعرض.
     *
     * المدخلات: PhotoRequestContract (Request) يحتوي على ملف واحد أو عدة ملفات صور.
     * المخرجات: PhotoCollectionContract (Resource Collection) يحتوي على الصور التي تم رفعها.
     *
     * ملاحظات:
     * - يدعم رفع عدة ملفات دفعة واحدة.
     * - يتم استخدام الـ Contract لتهيئة الـ Resource Collection للصور الجديدة.
     */
    public function store(PhotoRequestContract $request)
    {
        $photos = Photo::createPhotos($request->file('files'));
        // استخدم الـ contract للـ collection
        return app(PhotoCollectionContract::class, ["resource" => $photos]);
    }

    /**
     * حذف صورة أو عدة صور من المعرض بناءً على معرفاتها.
     *
     * المدخلات: PhotoGetRequestContract (Request) يحتوي على مجموعة معرفات الصور (IDs).
     * المخرجات: PhotoDeleteResponseContract (Response) يحتوي على حالة الحذف والمعرفات المحذوفة.
     *
     * ملاحظات:
     * - يدعم حذف عدة صور دفعة واحدة.
     * - يتم استخدام الـ Contract لتهيئة استجابة الحذف.
     */
    public function destroy(PhotoGetRequestContract $request)
    {
        $ids = $request->photoIds();
        $deleted = \Ahmed\GalleryImages\Models\Photo::deletePhotosByIds($ids);
        return app(PhotoDeleteResponseContract::class, ['messages' => 'secussfuly', 'deleted' => $deleted, 'ids' => $ids]);
    }
}
