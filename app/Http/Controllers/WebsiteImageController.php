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

    //    return $this->optimize($path);
    //    die;  
       $headers = array(
                         'Content-Type'        => 'image/'.$extension,
                         'Content-Description' => $filename
                       ); 

        $optimizerChain = OptimizerChainFactory::create();

        // Create a temporary file for the optimized image
        $tempPath = tempnam(sys_get_temp_dir(), 'optimized_image_');        

        // Optimize the image and save it to the temporary path
        $optimizerChain->optimize($path, $tempPath);


        // Return the optimized image as a file response
        // Use deleteFileAfterSend to clean up the temporary file after the response is sent
        return response()->file($tempPath, $headers)->deleteFileAfterSend(true);
   }


   public function optimize(Request $request)
    {
        // Image URL
        $imageUrl = '/home/u828600220/domains/sabsoft.com.pk/public_html/Retail/storage/images/products/1727867687-1726240292-ker-003.jpg';

        // Fetch the image from the provided URL
        $imageContents = file_get_contents($imageUrl);
        // $tempPath = 'tmp/' . uniqid() . '.jpg'; // Temporary path for the image

        // Store the original image temporarily
        // Storage::put($tempPath, $imageContents);

        // Optimize the image
        $image = Image::make(storage_path($imageContents));
        $image->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Save optimized image to a new path
        $optimizedPath = 'optimized/' . basename($tempPath);
        $image->save(storage_path('app/' . $optimizedPath), 80); // Save with 80% quality

        // Remove the temporary file
        Storage::delete($tempPath);

        // Show the optimized image
        return response()->file(storage_path('app/' . $optimizedPath));
    }
}