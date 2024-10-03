<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Image;

trait MediaTrait

{
    public function uploads($file, $path, $previousImage = "", $transformation = [])
    {
        if ($file) {

            $this->removeImage($path, $previousImage);
            $fileName   = time() . "-" . str_replace(' ', '', $file->getClientOriginalName());

            // Resize the image to 400x400 pixels
            if (!empty($transformation)) {
                $image = Image::make($file)->resize($transformation["width"], $transformation["height"], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();
            }
            // return response()->json(["filename" => $fileName]);
            Storage::disk('public')->put($path . $fileName, !empty($transformation) ? $image : File::get($file));
            $file_size = $this->fileSize($file);
            // $file->move(public_path('images/'.$path), $fileName);
            $file_name  = $file->getClientOriginalName();
            $file_type  = $file->getClientOriginalExtension();
            $filePath   = $path . $fileName;

            return $file = [
                'fileName' => $fileName,
                'fileType' => $file_type,
                'filePath' => $filePath,
                'fileSize' => $file_size,
            ];
        }
        // else{
        //     return 0;
        // }
    }

    public function fileSize($file, $precision = 2)
    {
        $size = $file->getSize();

        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        }

        return $size;
    }

    public function removeImage($path, $filename)
    {
        if ($filename != "") {
            if (Storage::disk('public')->exists($path . $filename)) {
                Storage::disk('public')->delete($path . $filename);
                return 1;
            }
        }
    }

    public function imageOptimize($path){
            // Create a new Image instance
            $imageContent = Storage::disk('public')->get($path);
            $img = Image::make($imageContent);
    
            // Resize and optimize the image
            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
    
            // Return the optimized image as a response
     return $img->response('webp', 85); // Adjust the format and quality as needed
  }
}
