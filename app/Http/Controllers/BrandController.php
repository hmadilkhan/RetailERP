<?php

namespace App\Http\Controllers;


use App\Brand;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Brand $brand){

        $lists = $brand->getBrand();

        return view('Inventory.brands.index',compact('lists'));
    }

    public function create(){

        return redirect()->route('brands.index');
    }

    //create folder image path
    public function create_folder($comFOldName)
    {
        // $path   = public_path('storage/images/').$comFOldName;
        $result = true;

        $directory = 'images/'.$comFOldName;
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // if (!File::isDirectory($path)) {
        //     $result = File::makeDirectory($path, 0777, true, true);
        // }

        return ($result) ? $directory : false;
    }

    public function store(Request $request){
     try{

         $imageBanner = null;
         $imageName   = null;

        $rules = [
                  'name'   => 'required',Rule::unique('brands')->where(function ($query) {
                                                return $query->where('company_id', session('company_id'));
                                            }),
                  'image'  => 'dimensions:width=128,height=128|mimes:jpg,jpeg,png,webp|max:1024',
                  'banner' => 'dimensions:width=1280,height=320|mimes:jpg,jpeg,png,webp|max:1024'
                ];

        $this->validate($request,$rules);

        if(!empty($request->image)){
            $returnImageVal = $this->saveBrandImage($request->image,['name'=>$request->name]);

            if(in_array($returnImageVal,[404,500])){

                   if($returnImageVal == 404){
                       $msg = 'Error! brand logo image path not found';
                   }

                   if($returnImageVal == 500){
                       $msg = 'Error! brand logo image not uploaded';
                   }

                  Session::flash('error',$msg);
                  return redirect()->route('brands.index');

            }else{
                $imageName = $returnImageVal;
            }
        }

        if(!empty($request->banner)){
            $returnImageVal = $this->saveBrandImage($request->banner,['name'=>$request->name.'-banner']);

            if(in_array($returnImageVal,[404,500])){

                   if($returnImageVal == 404){
                       $msg = 'Error! brand banner image path not found';
                   }

                   if($returnImageVal == 500){
                       $msg = 'Error! brand banner image not uploaded';
                   }

                  Session::flash('error',$msg);
                  return redirect()->route('brands.index');

            }else{
                $imageBanner = $returnImageVal;
            }
        }


        $save = Brand::create(array_merge(
                $request->except(["_token","image","banner","slug"]),
                ['image' => $imageName,'banner' => $imageBanner,'created_at' => date("Y-m-d H:i:s"),'created_at' => date("Y-m-d H:i:s"),
                'company_id' => session('company_id'),'slug'=>$this->removeSpecialCharacters((!empty($request->slug) ? $request->slug : $request->name))]));

        if(!$save){
            Session::flash('error','Error! record is not saved.');
            return redirect()->route('brands.index')->withInput();
        }

         Session::flash('success','Success!');
        return redirect()->route('brands.index');

      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('brands.index')->withInput();
      }
    }

    public function saveBrandImage($image,$extraValue){

            $process = true;
            $imageName = $this->removeSpecialCharacters($extraValue['name']).".".strtolower($image->getClientOriginalExtension());

            $path = $this->create_folder('brands/'.session('company_id'));

            if($path == null){
                return 404;
            }

            if(!Storage::disk('public')->move($image,$path)){
                return 500;
            }
        return $imageName;
    }

    public function removeOld_image($imageName){
        if (!Storage::disk('public')->exists('images/brands/'.session('company_id').'/'.$imageName)) {
            $result = Storage::disk('public')->delete('images/brands/'.session('company_id').'/'.$imageName);
            return ($result) ? true : false;
        }
        // $path = public_path('assets/images/').'/brands/'.session('company_id').'/'.$imageName;
        // if(File::exists($path)){
        //    return File::delete($path) ? true : false;
        // }

        return false;
    }

    public function edit($id){

      if($id == null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('brands.index');
      }

      $lists = Brand::where(['company_id'=>session('company_id'),'status'=>1])->get();

      $edit  = Brand::where(['id'=>$id,'company_id'=>session('company_id'),'status'=>1])->first();

     return view('Inventory.brands.index',compact('lists','id','edit'));
    }

    public function update(Request $request,$id){
     try{

         $imageBanner = null;
         $imageName   = null;

         $rules = ['name'=>'required'];

         $this->validate($request,$rules);

      if($id === null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('brands.index')->withInput();
      }

      $conditionApply_one = 'id ='.$id;

      $record = Brand::customQuery_allColumFetch($conditionApply_one,1,0); // "first" parameter condition apply filter by all column using strig format,  "second" parameter mode like '1' or '0' 1 mode return fetch one row and default 0 mode fetch all row return object format,  "third" parameter only work all record mode like '1' or '0'  1 mode return count value and default 0 mode return all record

      if($record === null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('brands.index')->withInput();
      }

      $conditionApply_two = 'id != '.$id.' and name = "'.$request->name.'" ';

      if(Brand::customQuery_allColumFetch($conditionApply_two,1,1) > 0){

        $rules = [
                  'name'   => 'required',Rule::unique('brands')->where(function ($query) {
                                                return $query->where('company_id', session('company_id'));
                                            }),
                  'image'  => 'dimensions:width=128,height=128|mimes:jpg,jpeg,png',
                  'banner' => 'dimensions:width=1280,height=320|mimes:jpg,jpeg,png'
                ];

        $this->validate($request,$rules);
      }


        if(!empty($request->image)){
            $returnImageVal = $this->saveBrandImage($request->image,['name'=>$request->name]);

            if(in_array($returnImageVal,[404,500])){

                   if($returnImageVal == 404){
                       $msg = 'Error! brand image path not found';
                   }

                   if($returnImageVal == 500){
                       $msg = 'Error! brand image not uploaded';
                   }

                  Session::flash('error',$msg);
                  return redirect()->route('brands.index');

            }else{
                $imageName = $returnImageVal;
            }
        }

        if(!empty($request->banner)){
            $returnImageVal = $this->saveBrandImage($request->banner,['name'=>$request->name.'-banner']);

            if(in_array($returnImageVal,[404,500])){

                   if($returnImageVal == 404){
                       $msg = 'Error! brand image path not found';
                   }

                   if($returnImageVal == 500){
                       $msg = 'Error! brand image not uploaded';
                   }

                  Session::flash('error',$msg);
                  return redirect()->route('brands.index');

            }else{
                $imageBanner = $returnImageVal;
            }
        }

          $recordUpdate = Brand::find($id);

          $recordUpdate->name     = $request->name;
          $recordUpdate->slug     = $this->removeSpecialCharacters((!empty($request->slug) ? $request->slug : $request->name));
          $recordUpdate->priority = $request->priority;
          $recordUpdate['parent'] = $request->post('parent');

          if($imageName != null){
              if($imageName != $recordUpdate->image){
                 $this->removeOld_image($recordUpdate->image);
              }
              $recordUpdate->image = $imageName;
          }

          if($imageBanner != null){
             if($imageBanner != $recordUpdate->banner){
                  $this->removeOld_image($recordUpdate->banner);
             }
                  $recordUpdate->banner = $imageBanner;
          }

          if($recordUpdate->save()){
              Session::flash('success','Success!');
          }else{
              Session::flash('error','error! record is not save');
          }

        return redirect()->route('brands.index');

      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('brands.index')->withInput();
      }
    }


    public function destroy($id){
      try{
          if($id == null){
              Session::flash('error','Error! record not found.');
              return redirect()->route('brands.index');
          }

          $conditionApply = 'id='.$id;

          $record = Brand::customQuery_allColumFetch($conditionApply,1,0);

          if($record == null){
              Session::flash('error','Error! record not found.');
              return redirect()->route('brands.index');
          }

          $getImageName = $record->image;

          if(Brand::find($id)->delete()){
              $this->removeOld_image($getImageName);

              DB::table('inventory_general')->where(['company_id'=>session('company_id'),'brand_id'=>$id])->update(['status'=>2]);

             Session::flash('success','Success!');
          }else{
             Session::flash('error','Error! record is not removed.');
          }

        return redirect()->route('brands.index');

      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('brands.index');
      }
    }

    public function removeSpecialCharacters($value){
        // Remove special characters except spaces
        $patternWithoutSpecialChars = preg_replace('/[^a-zA-Z0-9\s-]/', '',$value);

        // Remove extra spaces
        $patternWithoutExtraSpaces = preg_replace('/\s+/', '-', $patternWithoutSpecialChars);

        return strtolower($patternWithoutExtraSpaces);
    }

}
