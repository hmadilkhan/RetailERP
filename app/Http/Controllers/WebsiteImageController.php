<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizer\PngOptimizer;
use Image;

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

    //    return $this->Optimize_testing($filename);
    //    die;
       $headers = array(
                         'Content-Type'        => 'image/'.$extension,
                         'Content-Description' => $filename
                       );

        // $optimizerChain = OptimizerChainFactory::create();

        // // Create a temporary file for the optimized image
        // $tempPath = tempnam(sys_get_temp_dir(), 'optimized_image_');

        // // Optimize the image and save it to the temporary path
        // $optimizerChain->optimize($path, $tempPath);


        // Return the optimized image as a file response
        // Use deleteFileAfterSend to clean up the temporary file after the response is sent
        return response()->file($path, $headers)
   }

   public function Optimize_testing($image){

    // Original image path
    $imageUrl = Storage::disk('public')->path('images/products/' . $image);

    // Check if the original image exists
    if (!file_exists($imageUrl)) {
        return response()->json(['error' => 'Original image does not exist.'], 404);
    }

    // // Temporary folder path
    // $tmpPath = Storage::disk('public')->path('images/optimize_images');

    // // Ensure temporary directory exists
    // if (!is_dir($tmpPath)) {
    //     if (!mkdir($tmpPath, 0755, true)) {
    //         return response()->json(['error' => 'Unable to create temporary directory.'], 500);
    //     }
    // }

    // Temporary image path
    // $tmpImagePath = $tmpPath . '/' .$request->image;

    // // Copy original image to temporary folder
    // // Move the image
    // Storage::disk('public')->move('/images/products/' . $request->image,'images/optimize_images'.$request->image);

    // Optimize the image
    ImageOptimizer::optimize($imageUrl);
    // $optimizer = OptimizerChainFactory::create();
    // $optimizer->optimize($imageUrl);

    // Response headers
    $headers = [
        'Content-Type' => 'image/'.strtolower(pathinfo($image,PATHINFO_EXTENSION)),
    ];

    // Return the optimized image
    return response()->file($imageUrl, $headers);

    //      // Image URL
    //      $imageName = 'optimized-image.'.strtolower(pathinfo($request->image,PATHINFO_EXTENSION));
    //      $imageUrl = '/images/products/'.$request->image;
    //     //  Image::load(Storage::disk('public')->path($imageUrl))
    //     //  ->optimize()
    //     //  ->save($imageName);
    //       ImageOptimizer::optimize(Storage::disk('public')->path($imageUrl));

    //    // if you use a second parameter the package will not modify the original
    //     // ImageOptimizer::optimize($imageUrl, '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/optimize_images/'.$request->image);
    //     $headers = array(
    //         'Content-Type'        => 'image/jpg',
    //         'Content-Description' => $request->image
    //       );
    //     return response()->file(Storage::disk('public')->path($imageUrl), $headers);
    }

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

//    public function optimize(Request $request)
//     {
//         // Image URL
//         $imageUrl = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/products/1727867687-1726240292-ker-003.jpg';

//         // Fetch the image from the provided URL
//         $imageContents = file_get_contents($imageUrl);
//         // $tempPath = 'tmp/' . uniqid() . '.jpg'; // Temporary path for the image

//         // Store the original image temporarily
//         // Storage::put($tempPath, $imageContents);

//         // Optimize the image
//         $image = Image::make($imageContents);
//         $image->resize(800, null, function ($constraint) {
//             $constraint->aspectRatio();
//         });

//         // Save optimized image to a new path
//         $optimizedPath = 'optimized/' . basename($tempPath);
//         $image->save(storage_path('app/' . $optimizedPath), 80); // Save with 80% quality

//         // Remove the temporary file
//         Storage::delete($tempPath);

//         // Show the optimized image
//         return response()->file(storage_path('app/' . $optimizedPath));
//     }
}
