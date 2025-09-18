<?php

namespace Ahmed\GalleryImages;

class ComposerScripts
{
    /**
     * Execute after installing or updating the package.
     * Automatically copies important files to the user's Laravel project.
     *
     * @return void لا يوجد قيمة إرجاع
     *
     * ملاحظة: ينسخ Controller من الحزمة إلى مجلد Controllers في المشروع.
     */
    public static function copyFiles()
    {
        $packageBasePath = dirname(__DIR__); // مسار الحزمة src/
        $projectPath = getcwd(); // مسار مشروع Laravel الحالي

        // نسخ Controller
        $controllerSource = $packageBasePath . '/Controllers/PhotoController.php';
        $controllerDest = $projectPath . '/app/Http/Controllers/PhotoController.php';
        copy($controllerSource, $controllerDest);

        echo "✅ ملفات GalleryImagesPackage تم نسخها إلى مشروعك.\n";
    }
}
