<?php

namespace App\Http\Controllers;

use App\Models\Variation;
use Illuminate\Http\Request;
use App\posProducts;
use Image,Auth,File,Session;
use Illuminate\Support\Facades\DB;
use App\Helpers\custom_helper;

class VariationProductController extends Controller
{
 
    public function __construct(){

       $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $products  = DB::table('product_variable_relation as prod_var_rel')
                       ->join('product_variable_details as prod_var_dtl','prod_var_dtl.variable_id','prod_var_rel.id')
                       ->join('pos_products_gen_details as posProducts','posProducts.pos_item_id','prod_var_rel.pos_variable_id')
                       ->join('inventory_general as inventGeneral','inventGeneral.id','posProducts.product_id')
                       ->join('variations','variations.id','prod_var_rel.variation_id')
                       ->where(['variations.company_id'=>session('company_id'),'variations.status'=>1,'prod_var_rel.status'=>1])
                       ->select('prod_var_rel.id','prod_var_dtl.price','prod_var_dtl.image','variations.name as variat_name','posProducts.item_name as product_name','inventGeneral.product_name as parent_prod')
                       ->get();
          
      return view('variation-product.lists',compact('products'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(posProducts $posProducts)
    {
         $getposProduct = $posProducts->getposproducts();
         $variations    = Variation::where(['parent'=>0,'status'=>1,'company_id'=>session('company_id')])->get();
         
        return view('variation-product.create',compact('getposProduct','variations'));
    }

    public function getVariat_values(Request $request,posProducts $posProducts){

         return response()->json(Variation::where(['parent'=>$request->id,'company_id'=>session('company_id'),'status'=>1])->get(),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
           $rules = [
                      'posproduct'    => 'required',
                      'variation'     => 'required',
                      'variation_id'  => 'required',
                      // 'price'         => 'required',
                   ];      
    
           $this->validate($request,$rules);


           if(DB::table('product_variable_relation')->where(['variation_id'=>$request->post('variat_values'),'pos_variable_id'=>$request->post('posproduct'),'status'=>1])->count() > 0){
                  Session::flash('variat_values','This '.$request->post('variat_values_name').' variation values is already taken');
                
                  $rules = [
                             'variation_id'  => 'required|unique:product_variable_relation',
                          ];      
    
                  $this->validate($request,$rules);  
           }



           if($request->file('productimage')){
           	   $rules = [
                           'productimage'  => 'required|mimes:jpg,png,jpeg',
                         ];      
    
	           $this->validate($request,$rules);

	           $image     = $request->file('productimage');
	           $imageName = date('ymdhis').'.'.$image->getClientOriginalExtension();
	           $checkPath = $this->createImagePath();

	           if($checkPath == false){
	              Session::flash('error','Product image store location not found!');
	              return redirect()->back()->withInputs();
	           }


               if(!$image->move($checkPath,$imageName)){
                  // DB::table('product_variable_relation')->where('id',$result->id)->delete();
                  Session::flash('error','Image not uploaded! Server Issue.');
                  return redirect()->back()->withInputs();
               }	           
          }


           $result = DB::table('product_variable_relation')
                       ->insertGetId([
                                       'pos_variable_id' => $request->post('posproduct'),
                                       'variation_id'    => $request->post('variation_id'),
                                       'variation'       => $request->post('variat_values_name'),
                                    ]);

             if(!$result){
                  Session::flash('error','Server Issue');
                  return redirect()->back()->withInputs();
             }

            $prod_variat = DB::table('product_variable_details')
                             ->insert([
                                     'variable_id' => $result,
                                     'price'       => $request->post('price'),
                                     'image'       => isset($imageName) ? $imageName : null,
                                     'created_at'  => date('Y-m-d H:i:s')
                                      ]);
            if(!$prod_variat){
                  DB::table('product_variable_relation')->where('id',$result)->delete();
              
                    if(isset($imageName)){
                    	 unlink($checkPath.'/'.$imageName);
                    }

                  Session::flash('error','Server Issue');
                  return redirect()->back()->withInputs();
            }
            
            Session::flash('success','Success!');
            return redirect()->route('listVariatProduct');             				
    }

    public function edit(posProducts $posProducts,$id){
   
         $getposProduct = $posProducts->getposproducts();
         $variations    = Variation::where(['status'=>1,'parent'=>0,'company_id'=>Auth::user()->company_id])->get();

         $variatProd    = DB::table('product_variable_relation as prod_var_rel')
                             ->join('product_variable_details as prod_var_dtl','prod_var_dtl.variable_id','prod_var_rel.id')
                             // ->join('pos_products_gen_details as posProducts','posProducts.pos_item_id','prod_var_rel.pos_variable_id')
                             ->join('variations','variations.id','prod_var_rel.variation_id')
                             ->select(DB::raw('prod_var_rel.id,prod_var_rel.pos_variable_id,prod_var_dtl.price,prod_var_rel.variation_id,prod_var_rel.variation,prod_var_dtl.image,variations.parent'))
                             ->where('prod_var_rel.id',$id)
                             ->first();
         
        return view('variation-product.edit',compact('getposProduct','variations','variatProd'));
    }


    public function createImagePath(){
         $path   = public_path('assets/images/variation-product/').Auth::user()->company_id;
         $result = true;
          if(!File::isDirectory($path)){
              $result = File::makeDirectory($path, 0777, true, true);
          }  

        return ($result) ? $path : false; 
    }

    public function update(Request $request,$id){

      $check = DB::table('product_variable_relation')
                    ->join('product_variable_details','product_variable_details.variable_id','product_variable_relation.id')
                    ->where('product_variable_relation.id',$id)
                    ->first();

      if($check == null){
          Session::flash('error','Error! record not found!');
          return redirect()->route('listVariatProduct');
      }

           $rules = [
                      'posproduct'    => 'required',
                      'variation'     => 'required',
                      'variat_values' => 'required',
                      // 'price'         => 'required',
                   ];      
    
           $this->validate($request,$rules);

           if(DB::table('product_variable_relation')->where(['variation_id'=>$request->post('variat_values'),'pos_variable_id'=>$request->post('posproduct'),'status'=>1])->where('id','!=',$id)->count() > 0){
                  Session::flash('variat_values','This '.$request->post('variat_values_name').' variation values is already taken');
                
                  $rules = [
                             'variation_id'  => 'required|unique:product_variable_relation',
                          ];      
    
                  $this->validate($request,$rules);  
           }

              $result  =  DB::table('product_variable_relation')
                             ->where('id',$id)
                             ->update([
                                         'pos_variable_id' => $request->post('posproduct'),
                                         'variation_id'    => $request->post('variat_values'),
                                         'variation'       => $request->post('variat_values_name'),
                                      ]);


         if($request->file('productimage')){

              $rules = [
                         'productimage'  => 'required|mimes:jpg,png,jpeg',
                      ];      
    
              $this->validate($request,$rules);          

             $image     = $request->file('productimage');
             $imageName = date('ymdhis').'.'.$image->getClientOriginalExtension();
             $checkPath = $this->createImagePath();

             if(file_exists($checkPath.'/'.$check->image) && $check->image != null){
                 unlink($checkPath.'/'.$check->image);
             }

             if(!$image->move($checkPath,$imageName)){
                  Session::flash('error','Image not uploaded! Server Issue.');
                  return redirect()->back()->withInputs();
             }

         }

           $prod_variat = DB::table('product_variable_details')
                             ->where('variable_id',$id) 
	                         ->update([
                                     'price'       => $request->post('price'),
                                     'image'       => isset($imageName) ? $imageName : $check->image,
                                     'created_at'  => date('Y-m-d H:i:s')
                                      ]);

         Session::flash('success','Success!');  
        return redirect()->route('listVariatProduct'); 
    }    

    public function destroy(Request $request,$id){
    //   return $id;
      if(DB::table('product_variable_relation')->update(['status'=>0])->where('id',$id)){
           Session::flash('success','Success!');
      }else{
         Session::flash('error','Error! Server Issue');  
      }
       
      return redirect()->route('listVariatProduct');  
    }  



    public function imageView(Request $request){

          $path      = public_path('assets/images/variation-product/').Auth::user()->company_id.'/'.$request->filename;
          $filename  = $request->filename;
          $extension = strtolower(pathinfo($request->filename,PATHINFO_EXTENSION));

          if(!File::exists($path)){
              $filename  = 'no-image.png';
              $extension = 'png';
              $path      =  public_path('assets/images/variation-product/').$filename; 
          }

           $headers = array(
                                  'Content-Type'        => 'image/'.$extension,
                                  'Content-Description' => $filename
                                ); 

          return response()->file($path, $headers); 
    } 


}
