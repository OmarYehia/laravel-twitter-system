<?php
namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    private function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);
        $file = $uploadedFile->storeAs($folder, $name . '.' . $uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }

    /**
     * Uploads image to /public/uploads/images
     *
     * @param Illuminate\Http\Request $requestData
     * @return String $filePath a file path to be saved in DB
     */
    private function uploadImageToServer($requestData)
    {
        $image = $requestData->file('image');
        $nameSlug = Str::slug($requestData->name) . "_" . time();
        $folder = '/uploads/images/';
        $filePath = $folder . $nameSlug . "." . $image->getClientOriginalExtension();
        $file = $this->uploadOne($image, $folder, 'public', $nameSlug);
                
        return $filePath;
    }
}
