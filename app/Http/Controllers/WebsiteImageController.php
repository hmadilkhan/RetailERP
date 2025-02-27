<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;
// use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
// use Spatie\ImageOptimizer\OptimizerChainFactory;
// use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
// use Spatie\ImageOptimizer\Optimizer\PngOptimizer;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
// use Image;

class WebsiteImageController extends Controller
{

    // ===============================================
    //         image show module //
    // ===============================================

    public function show_image_website(Request $request){

        $mode      = $request->mode;
        // $compId    = $request->compId;
        $filename  = $request->filename;
        $webid     = $request->webid != null ? explode('-',$request->webid) : null;
        // =====================================================
        $extension = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        $path = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/';
       // =====================================================

              // Error image format
       // =====================================================
        if(!in_array($extension,['jpeg','jpg','gif','png','webp'])){
           return response()->json('Invalid image format!',500);
        }
      // =====================================================

       if($mode == 'slider'){
            if($webid == null){
               $extension = 'png';
               $filename  = 'no-image.png';
               $path     .= $filename;
               $headers = array(
                                 'Content-Type'        => 'image/'.$extension,
                                 'Content-Description' => $filename
                               );

               return response()->file($path, $headers);
            }

          $path .= 'website/sliders/'.$webid[0].'/'.$webid[1].'/'.$filename;

       }elseif($mode == 'advertisement'){

            if($webid == null){
               $extension = 'png';
               $filename  = 'no-image.png';
               $path     .= $filename;
               $headers = array(
                                 'Content-Type'        => 'image/'.$extension,
                                 'Content-Description' => $filename
                               );

               return response()->file($path, $headers);
            }

           $path .= 'website/advertisements/'.$webid[0].'/'.$webid[1].'/'.$filename;
       }elseif($mode == 'review'){
           $path .= 'customer-reviews/'.$filename;
       }elseif($mode == 'department'){
           $path .= 'department/'.$filename;
       }elseif($mode == 'prod'){
           $path .= 'products/'.$filename;
       }elseif($mode == 'prodvariation'){

            if($webid == null){
               $extension = 'png';
               $filename  = 'no-image.png';
               $path     .= $filename;
               $headers = array(
                                 'Content-Type'        => 'image/'.$extension,
                                 'Content-Description' => $filename
                               );

               return response()->file($path, $headers);
            }

           $path .= 'variation-product/'.$webid[0].'/'.$filename;
       }elseif($mode == 'tag'){
           $path .= 'tags/'.$filename;
       }elseif($mode == 'brand'){

            if($webid == null){
               $extension = 'png';
               $filename  = 'no-image.png';
               $path     .= $filename;
               $headers = array(
                                 'Content-Type'        => 'image/'.$extension,
                                 'Content-Description' => $filename
                               );

               return response()->file($path, $headers);
            }

           $path .= 'brands/'.$webid[0].'/'.$filename;
       }elseif($mode == 'testimonial'){
           $path .= 'testimonials/'.$filename;
       }else{
           $path .= 'website/'.$filename;
       }

       if(!File::exists($path)){
          $extension = 'png';
          $filename  = 'no-image.png';
          $path      = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/'.$filename;

       }

       if(in_array($extension,['jpg','jpeg'])){
         return $this->OptimizeImage($filename,$path);
       }

       $headers = array(
                         'Content-Type'        => 'image/'.$extension,
                         'Content-Description' => $filename
                       );


        return response()->file($path, $headers);
   }

//    public function Optimize_testing($image){

//     // Original image path
//     $imageUrl = Storage::disk('public')->path('images/products/' . $image);

//     // Check if the original image exists
//     if (!file_exists($imageUrl)) {
//         return response()->json(['error' => 'Original image does not exist.'], 404);
//     }

//     // // Temporary folder path
//     // $tmpPath = Storage::disk('public')->path('images/optimize_images');

//     // // Ensure temporary directory exists
//     // if (!is_dir($tmpPath)) {
//     //     if (!mkdir($tmpPath, 0755, true)) {
//     //         return response()->json(['error' => 'Unable to create temporary directory.'], 500);
//     //     }
//     // }

//     // Temporary image path
//     // $tmpImagePath = $tmpPath . '/' .$request->image;

//     // // Copy original image to temporary folder
//     // // Move the image
//     // Storage::disk('public')->move('/images/products/' . $request->image,'images/optimize_images'.$request->image);

//     // Optimize the image
//     ImageOptimizer::optimize($imageUrl);
//     // $optimizer = OptimizerChainFactory::create();
//     // $optimizer->optimize($imageUrl);

//     // Response headers
//     $headers = [
//         'Content-Type' => 'image/'.strtolower(pathinfo($image,PATHINFO_EXTENSION)),
//     ];

//     // Return the optimized image
//     return response()->file($imageUrl, $headers);

//     //      // Image URL
//     //      $imageName = 'optimized-image.'.strtolower(pathinfo($request->image,PATHINFO_EXTENSION));
//     //      $imageUrl = '/images/products/'.$request->image;
//     //     //  Image::load(Storage::disk('public')->path($imageUrl))
//     //     //  ->optimize()
//     //     //  ->save($imageName);
//     //       ImageOptimizer::optimize(Storage::disk('public')->path($imageUrl));

//     //    // if you use a second parameter the package will not modify the original
//     //     // ImageOptimizer::optimize($imageUrl, '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/optimize_images/'.$request->image);
//     //     $headers = array(
//     //         'Content-Type'        => 'image/jpg',
//     //         'Content-Description' => $request->image
//     //       );
//     //     return response()->file(Storage::disk('public')->path($imageUrl), $headers);
//     }

// public function Optimize_testing(Request $request) {
//     // Image URL
//     $originalImageUrl = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/products/'.$request->image;
//     $optimizedImageUrl = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/optimize_images/'.$request->image;

//     // Copy original image to the optimize folder
//     if (!rename($originalImageUrl, $optimizedImageUrl)) {
//         // Handle the error if the copy fails
//         return response()->json(['error' => 'Failed to copy the original image.'], 500);
//     }

//     // Optimize the copied image
//     ImageOptimizer::optimize($optimizedImageUrl);

//     $headers = array(
//         'Content-Type'        => 'image/jpg',
//         'Content-Description' => $request->image
//     );

//     return response()->file($optimizedImageUrl, $headers);
// }

   public function OptimizeImage($imageName,$path)
    {
        // Image URL
        // $imageUrl = $path; // Original image path
        // $optimizedPath = Storage::disk('public')->path('images/optimize_images/' . $imageName);

        // // Ensure the source image exists
        // if (File::exists($imageUrl)) {
        //     // Copy the image to the new location
        //     copy($imageUrl, $optimizedPath);

        //     // Determine the image type and process accordingly
        //     $extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        //     if ($extension === 'jpg' || $extension === 'jpeg') {
        //         // Process the copied JPEG image
        //         $process = new Process(['jpegoptim', '--max=40', $optimizedPath]);
        //     } elseif ($extension === 'png') {
        //         // Process the copied PNG image
        //         $process = new Process(['optipng', '-02', $optimizedPath]);
        //     } else {
        //         throw new \Exception("Unsupported image format: {$extension}");
        //     }

        //     // Run the optimization process
        //     $process->run();

        //     // Check if the process was successful
        //     if (!$process->isSuccessful()) {
        //         // Capture error output if it fails
        //         $errorOutput = $process->getErrorOutput();
        //         throw new ProcessFailedException($process, $errorOutput);
        //     }

        //     // If it's a PNG or JPEG, convert to WEBP after optimization
        //     if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg') {
        //         $webpPath = Storage::disk('public')->path('images/optimize_images/' . pathinfo($imageName, PATHINFO_FILENAME) . '.webp');
        //         $convertProcess = new Process(['cwebp', '-q', '80', $optimizedPath, '-o', $webpPath]);
        //         $convertProcess->run();

        //         // Check if the conversion process was successful
        //         if (!$convertProcess->isSuccessful()) {
        //             $errorOutput = $convertProcess->getErrorOutput();
        //             throw new ProcessFailedException($convertProcess, $errorOutput);
        //         }

        //         // Return the WEBP image
        //         return response()->file($webpPath)->deleteFileAfterSend(true);
        //     }

        //     // Return the optimized image path
        //     return response()->file($optimizedPath)->deleteFileAfterSend(true);
        // } else {
        //     throw new \Exception("Image file does not exist: {$imageUrl}");
        // }




        $imageUrl = $path;
        $optimizedPath = Storage::disk('public')->path('images/optimize_images/'.$imageName);

        // Ensure the source image exists
        if (File::exists($path)) {
            // Copy the image to the new location
            copy($path, $optimizedPath);

            // Now process the copied image
            $process = new Process(['jpegoptim', '--max=75', $optimizedPath]);
            $process->run();
            // Check if the process was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            //   if(strtolower(pathinfo($imageName,PATHINFO_EXTENSION)) == 'png'){
            //     // Now process the copied image
            //     $process = new Process(['optipng', '-02', $optimizedPath]);
            //      // Capture the output and error
            //     $output = $process->getOutput();
            //     $errorOutput = $process->getErrorOutput();

            //     // Check if the process was successful
            //     if (!$process->isSuccessful()) {
            //         throw new ProcessFailedException($process, $output . $errorOutput);
            //     }
            //   }
            $headers = array(
                'Content-Type'        => 'image/'.strtolower(pathinfo($imageName,PATHINFO_EXTENSION)),
                'Content-Description' => $imageName,
                'Cache-Control'       => 'public, max-age=604800',
              );
            // Show the optimized image path
            return response()->file($optimizedPath,$headers)->deleteFileAfterSend(true);
        } else {
            throw new \Exception("Image file does not exist: {$imageName}");
        }

    }
}
