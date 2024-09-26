<?php

namespace App\Http\Controllers;


use App\Section;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;


class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    
    public function index(Section $section){
        
        $lists = Section::getSection();
        
        return view('Invent_Department.Section.index',compact('lists'));
    }
    
    public function create(){
        
        return redirect()->route('sections.index');
    }
    
    
    public function store(Request $request){
     try{    
        $rules = [
                  'name' => 'required',Rule::unique('sections')->where(function ($query) {
                                                return $query->where('company_id', session('company_id'));
                                            })
                ];
        
        $this->validate($request,$rules);  
        

        $save = Section::create(array_merge(
                $request->except(["_token"]),
                ['created_at' => date("Y-m-d H:i:s"),'created_at' => date("Y-m-d H:i:s"),'company_id' => session('company_id')]));
                
        if(!$save){
            Session::flash('error','Error! record is not saved.');
            return redirect()->route('sections.index')->withInput();
        }       
        
         Session::flash('success','Success!');
        return redirect()->route('sections.index');
        
      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('sections.index');
      }
    }
    
    public function edit($id){
        
      if($id == null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('sections.index');
      }   
      
      $lists = Section::where(['company_id'=>session('company_id')])->get();
      
      $edit  = Section::where(['id'=>$id,'company_id'=>session('company_id')])->first();
        
     return view('Invent_Department.Section.index',compact('lists','id','edit'));   
    }
    
    
    public function update(Request $request,$id){
     try{        
         
         $rules = ['name'=>'required'];

         $this->validate($request,$rules);

      if($id === null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('sections.index')->withInput();
      } 
      
      $conditionApply_one = 'id ='.$id;
    
      $record = Section::customQuery_allColumFetch($conditionApply_one,1,0); // "first" parameter condition apply filter by all column using strig format,  "second" parameter mode like '1' or '0' 1 mode return fetch one row and default 0 mode fetch all row return object format,  "third" parameter only work all record mode like '1' or '0'  1 mode return count value and default 0 mode return all record

      if($record === null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('sections.index')->withInput();          
      }
      
      $conditionApply_two = 'id != '.$id.' and name = "'.$request->name.'" ';
       
      if(Section::customQuery_allColumFetch($conditionApply_two,1,1) > 0){

        $rules = [
                  'name' => 'required',Rule::unique('sections')->where(function ($query) {
                                                return $query->where('company_id', session('company_id'));
                                            })
                ];
                
        $this->validate($request,$rules);            
      }

          $recordUpdate = Section::find($id);
           
          $recordUpdate->name     = $request->name;

          if($recordUpdate->save()){
              Session::flash('success','Success!');  
          }else{
              Session::flash('error','error! record is not save');
          }

        return redirect()->route('sections.index');
        
      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('sections.index')->withInput();
      }  
    }     
    
    public function destroy($id){
     try{        
      if($id == null){
          Session::flash('error','Error! record not found.');
          return redirect()->route('sections.index');
      }
      
      if(Section::where(['id'=>$id,'company_id'=>session('company_id')])->delete()){
            
         Session::flash('success','Success!'); 
      }else{
         Session::flash('error','Error! record is not removed.'); 
      }
      
        return redirect()->route('sections.index');
        
      }catch(Exception $e){
          Session::flash('error','Error! '.$e->getMessage());
          return redirect()->route('sections.index');
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