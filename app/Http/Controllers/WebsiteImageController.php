<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;

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
        $path = Storage::disk('public')->get('/images/');
       // =====================================================

              // Error image format
       // ===================================================== 
        if(!in_array($extension,['jpeg','jpg','png','webp'])){
           return response()->json('Invalid image format!',500);
        }
      // =====================================================  
      
       if($mode == 'slider'){
            if($webid == null){
               $path      .= 'no-image.png';
               $headers = array(
                                 'Content-Type'        => 'image/png',
                                 'Content-Description' => 'no-image.png'
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

       if(!Storage::disk('public')->exists($path)){
          $extension = 'png';
          $filename  = 'no-image.png';
          $path      = Storage::disk('public')->path('/images/'.$filename);

       }

       $headers = array(
                         'Content-Type'        => 'image/'.$extension,
                         'Content-Description' => $filename
                       ); 

     return response()->file($path, $headers); 
   }

}