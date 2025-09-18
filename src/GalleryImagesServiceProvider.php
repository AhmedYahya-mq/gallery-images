<?php

namespace Ahmed\GalleryImages;

use Illuminate\Support\ServiceProvider;

class GalleryImagesServiceProvider extends ServiceProvider
{
    public $bindings = [
        // ربط الواجهة PhotoRequestContract مع الكلاس الذي ينفذها
        \Ahmed\GalleryImages\Contracts\PhotoRequestContract::class => \Ahmed\GalleryImages\Http\Requests\PhotoRequest::class,
        // ربط الواجهة PhotoCollectionContract مع الكلاس الذي ينفذها
        \Ahmed\GalleryImages\Contracts\PhotoCollectionContract::class => \Ahmed\GalleryImages\Http\Resources\PhotoCollection::class,

        // ربط الواجهة PhotoResourceContract مع الكلاس الذي ينفذها
        \Ahmed\GalleryImages\Contracts\PhotoResourceContract::class => \Ahmed\GalleryImages\Http\Resources\PhotoResource::class,
        \Ahmed\GalleryImages\Contracts\PhotoDeleteResponseContract::class => \Ahmed\GalleryImages\Http\Responses\PhotoDeleteResponse::class,

        \Ahmed\GalleryImages\Contracts\PhotoShowResponseContract::class => \Ahmed\GalleryImages\Http\Responses\PhotoShowResponse::class,


        \Ahmed\GalleryImages\Contracts\PhotoGetRequestContract::class => \Ahmed\GalleryImages\Http\Requests\PhotoGetRequest::class,

    ];
    /**
     * Bootstrap any package services.
     *
     * @return void لا يوجد قيمة إرجاع
     *
     * ملاحظة: يحمل الـ migrations والـ routes وينشر الملفات عند الحاجة.
     */
    public function boot()
    {
        // تحميل Migration الخاصة بالحزمة
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // تحميل Routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        // $this->loadRoutesFrom(__DIR__ . '/routes/api.php');


        // Controllers
        $this->publishes([
            __DIR__ . '/Controllers/' => app_path('Http/Controllers'),
        ], 'controllers');
        // migrations
        $this->publishes([
            __DIR__ . '/database/' => database_path('migrations'),
        ], 'migrations');

    }

    /**
     * Register bindings and services in the container.
     *
     * @return void لا يوجد قيمة إرجاع
     *
     * ملاحظة: يربط الواجهات مع الكلاسات المنفذة لها.
     */
    public function register()
    {
        foreach ($this->bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }
}
