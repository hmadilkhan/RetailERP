<?php

namespace App\Http\Controllers;


use App\Tag;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use App\Traits\MediaTrait;
use Auth,File;


class TagController extends Controller
{
    use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    
    public function index(Tag $tag){
        
        $lists = $tag->getTags();
        
        return view('Inventory.tags.index',compact('lists'));
    }
    
    public function create(){
        
        return redirect()->route('tags.index');
    }
    
    
    public function store(Request $request){
     try{   
        $desktop_banner = ''; 
        $mobile_banner = '';
        $rules = [
                  'name' => 'required',Rule::unique('tags')->where(function ($query) {
                                                return $query->where('company_id', session('company_id'));
                                            }),
                   'desktop_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
                   'mobile_banner'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',                         
                ];
        
        $this->validate($request,$rules); 
        
        
        if (!empty($request->file('desktop_banner'))) { //desktop image
            $file = $this->uploads($request->file('desktop_banner'), "images/tags/");
            $desktop_banner = !empty($file) ? $file["fileName"] : "";
        }        
        
        if (!empty($request->file('mobile_banner'))) { //mobile image
            $file = $this->uploads($request->file('mobile_banner'), "images/tags/");
            $mobile_banner = !empty($file) ? $file["fileName"] : "";
        }  

        $save = Tag::create(array_merge(
                $request->except(["_token","slug","desktop_banner","mobile_banner"]),
                [
                'created_at' => date("Y-m-d H:i:s"),'created_at' => date("Y-m-d H:i:s"),
                'company_id' => session('company_id'),
                'slug'=>$this->removeSpecialCharacters((!empty($request->slug) ? $request->slug : $request->name)),
                'desktop_banner'=>$desktop_banner,'mobile_banner'=>$mobile_banner,
               ]));
                
        if(!$save){
            if(File::exists('storage/images/tags/'.$desktop_banner)){
                File::delete('storage/images/tags/'.$desktop_banner);
            }

            if(File::exists('storage/images/tags/'.$mobile_banner)){
                File::delete('storage/images/tags/'.$mobile_banner);
            }

            Session::flash('error','Error! record is not saved.');
            return redirect()->route('tags.index')->withInput();
        }       
        
         Session::flash('success','Success!');
        return redirect()->route('tags.index');
        
      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('tags.index');
      }
    }
    
    public function edit($id){
        
      if($id == null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('tags.index');
      }   
      
      $lists = Tag::where(['company_id'=>session('company_id'),'status'=>1])->get();
      
      $edit  = Tag::where(['id'=>$id,'company_id'=>session('company_id'),'status'=>1])->first();
        
     return view('Inventory.tags.index',compact('lists','id','edit'));   
    }
    
    
    public function update(Request $request,$id){
     try{        
         
         $mobile_banner = null;
         $desktop_banner = null;

         $rules = ['name'=>'required'];

         $this->validate($request,$rules);

      if($id === null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('tags.index')->withInput();
      } 
      
      $conditionApply_one = 'id ='.$id;
    
      $record = Tag::customQuery_allColumFetch($conditionApply_one,1,0); // "first" parameter condition apply filter by all column using strig format,  "second" parameter mode like '1' or '0' 1 mode return fetch one row and default 0 mode fetch all row return object format,  "third" parameter only work all record mode like '1' or '0'  1 mode return count value and default 0 mode return all record

      if($record === null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('tags.index')->withInput();          
      }
      
      $conditionApply_two = 'id != '.$id.' and name = "'.$request->name.'" ';
       
      if(Tag::customQuery_allColumFetch($conditionApply_two,1,1) > 0){

        $rules = [
                  'name' => 'required',Rule::unique('tags')->where(function ($query) {
                                                return $query->where('company_id', session('company_id'));
                                            }),
                  'desktop_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
                  'mobile_banner'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',                         
         ]; 

        $this->validate($request,$rules);            
      }
      
      $recordUpdate = Tag::find($id);     
           
          $recordUpdate->name     = $request->name;
          $recordUpdate->slug     = $this->removeSpecialCharacters((!empty($request->slug) ? $request->slug : $request->name));
          $recordUpdate->priority = $request->priority;
          $recordUpdate->meta_title       = $request->meta_title;
          $recordUpdate->meta_description = $request->meta_description;

          if(!empty($request->desktop_banner)){
            $file = $this->uploads($request->file('desktop_banner'), "images/tags/",$recordUpdate->desktop_banner);
            $desktop_banner = !empty($file) ? $file["fileName"] : "";  
            if(!empty($desktop_banner)){          
            $recordUpdate->desktop_banner = $desktop_banner;
            }
         } 

    
          if(!empty($request->mobile_banner)){
            $file = $this->uploads($request->file('mobile_banner'), "images/tags/",$recordUpdate->mobile_banner);
            $mobile_banner = !empty($file) ? $file["fileName"] : "";  
            if(!empty($mobile_banner)){          
              $recordUpdate->mobile_banner = $mobile_banner;
            }    
          }

          if($recordUpdate->save()){
              Session::flash('success','Success!');  
          }else{
              Session::flash('error','error! record is not save');
          }

        return redirect()->route('tags.index')->withInput();
        
      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('tags.index')->withInput();
      }  
    }     
    
    public function destroy($id){
     try{        
      if($id == null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('tags.index');
      }
      
      if(Tag::where(['id'=>$id,'company_id'=>session('company_id'),'status'=>1])->update(['status'=>0,'updated_at'=>now()])){
            
         Session::flash('success','Success!'); 
      }else{
         Session::flash('error','Error! record is not removed.'); 
      }
      
        return redirect()->route('tags.index');
        
      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('tags.index');
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