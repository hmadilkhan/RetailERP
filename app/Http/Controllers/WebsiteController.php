<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebsiteDetail;
use App\WebsiteProduct;
use App\Models\InventoryDepartment;
use App\Models\Company;
use App\branch;
use App\Terminal;
use App\Traits\MediaTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Session, Image, Auth, Validator, File;
use Illuminate\Support\Facades\Storage;

class WebsiteController extends Controller
{
    use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view("websites.index", [
            "websites" => WebsiteDetail::with("company")->where('status', 1)->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view("websites.create", [
            "companies" => Company::all()
        ]);
    }

    // public function show(Request $request)
    // {
    // return 2;
    // }

    public function store(Request $request)
    {
        // |regex:/^[a-zA-Z]+$/u
        $this->validate($request, [
            "company_id"  => "required",
            "type"        => "required",
            // "theme"       => "required",
            "name"        => "required|max:255|unique:website_details",
            "url"         => "required",
            // "logo"        => "required",
            // "favicon"     => "required",
        ]);

        try {

            if (WebsiteDetail::where(['company_id' => $request->company_id, 'status' => 1, 'name' => $request->name])->count() > 0) {

                $this->validate($request, [
                    "name"        => "required|max:255|unique:website_details",
                ]);
            }

            $imageFavicon = null;
            $imageLogo    = null;

            $websiteName  = strtolower(str_replace(array(" ", "'"), '-', $request->post('name')));

            if (!empty($request->logo)) {
                $request->validate([
                    'logo' => 'mimes:jpeg,png,jpg,webp|max:1024',
                ]);
               $imageLogo = $this->uploads($request->file('logo'),'images/website/');
            }

            if (!empty($request->favicon)) {
                $request->validate([
                    'favicon' => 'mimes:jpeg,png,jpg,webp|max:1024',
                ]);
                $imageFavicon = $this->uploads($request->file('favicon'),'images/website/');
            }


            $website = WebsiteDetail::create(array_merge(
                $request->except(["_token", "step", "logo", "favicon"]),
                ['logo' => (!empty($imageLogo) ? $imageLogo['fileName'] : ''), 'favicon' => (!empty($imageFavicon) ? $imageFavicon['fileName'] : ''), 'is_open' => 1]
            ));


            if (!isset($website->id)) {
                if (!empty($imageFavicon)) {
                    $this->removeImage("images/website/",$imageFavicon['fileName']);
                }

                if (!empty($imageLogo)) {
                    $this->removeImage("images/website/",$imageLogo['fileName']);
                }

                Session::flash('error', 'Server issue');
                return redirect()->route("website.create");
            }


            DB::table('website_theme')
                ->insert([
                    'website_id'      => $website->id,
                    'name'            => $request->post('type'),
                    'fontstyle'       => 'Poppins',
                    'font_url'        => 'https://fonts.googleapis.com/css2?family=Poppins&family=Roboto&family=Corben&display=swap',
                    'product_list'    => 2,
                    'cart_layout'     => 1,
                    'back_to_top_btn' => 1,
                    'location_modal'  => 0,
                    'top_contact_box' => 1,
                    'product_view'    => 'page_view'
                ]);

            Session::flash('success', 'Success!');
            return redirect()->route("website.index");
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->route("website.create");
        }
    }

    public function edit(Request $request, $id)
    {

        $website_detail = WebsiteDetail::where('website_details.id', $id)
            ->join('company', 'company.company_id', 'website_details.company_id')
            ->select('website_details.*', 'company.name as company_name')
            ->first();
        if ($website_detail == null) {

            Session::flash('error', 'Record not found!');
            return redirect()->route('website.index');
        }


        return view("websites.edit", [
            "website" => $website_detail,
            // "companies" => Company::all()
        ]);
    }

    public function update(Request $request, $id)
    {

        $website_detail = WebsiteDetail::find($id);

        $this->validate($request, [
            // "company_id"  => "required",
            "type"        => "required",
            // "theme"       => "required",
            "url"         => "required",
        ]);

        try {
            $imageLogo    = null;
            $imageFavicon = null;
            $websiteName  = strtolower(str_replace(array(" ", "'"), '-', $request->post('name')));

            if (!empty($request->favicon)) {
                $request->validate([
                    'favicon' => 'mimes:jpeg,png,jpg,webp|max:1024',
                ]);

                $imageFavicon = $this->uploads($request->file('favicon'),'images/website/',$website_detail->favicon);
            }

            if (!empty($request->logo)) {

                $request->validate([
                    'logo' => 'mimes:jpeg,png,jpg,webp|max:1024',
                ]);

                $imageLogo =$this->uploads($request->file('logo'),'images/website/',$website_detail->logo);
            }


            if ($website_detail->name  != $request->name) {
                // regex:/^[a-zA-Z]+$/u
                $rule = [
                    "name" => "required|max:255|unique:website_details",
                ];
                $this->validate($request, $rule);
            }

            $website_detail->type        = $request->type;
            $website_detail->name        = $request->name;
            $website_detail->url         = $request->url;
            $website_detail->whatsapp    = $request->whatsapp;
            $website_detail->uan_number  = $request->uan_number;

            if (!empty($imageLogo)) {
                $website_detail->logo   = $imageLogo['fileName'];
            }

            if (!empty($imageFavicon)) {
                $website_detail->favicon  = $imageFavicon['fileName'];
            }

            $website_detail->save();


            return redirect()->route("website.index");
        } catch (Exception $e) {
            return redirect()->route("website.edit", $website_detail->id);
        }
    }

    public function destroy(Request $request, $id)
    {
        $getRecord = WebsiteDetail::find($id);

        if ($getRecord == null) {
            Session::flash('error', 'Error! record not found! Server Issue!');
            return redirect()->route("website.index");
        }

        $getRecord->status = 0;

        if ($getRecord->save()) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Error! this ' . $getRecord->name . ' website is not removed!');
        }
        return redirect()->route("website.index");
    }

    // ======================================================================================
    //                  Website Slider Modules
    //=======================================================================================

    public function getSlider(Request $request)
    {

        $companyId = session('company_id');

        return view("websites.sliders.index", [
            "websites"          => WebsiteDetail::where('company_id', $companyId)->get(),
            "departments"       => InventoryDepartment::where('company_id', $companyId)->get(),
            "websiteSlider"     => DB::table('website_sliders')
                ->join('website_details', 'website_details.id', 'website_sliders.website_id')
                ->select('website_details.*')
                ->where('website_details.company_id', $companyId)
                ->where('website_sliders.status', 1)
                ->where('website_sliders.type', 'default')
                ->groupBy('website_details.name')
                ->get(),
            "websiteDeaprtmentSlider"  => DB::table('website_sliders')
                ->join('inventory_department', 'inventory_department.department_id', 'website_sliders.department_slider')
                ->join('website_details', 'website_details.id', 'website_sliders.website_id')
                ->select('website_details.*','inventory_department.department_name as department_slider_name','website_sliders.department_slider')
                ->where('website_details.company_id', $companyId)
                ->where('website_sliders.status', 1)
                ->where('website_sliders.type', 'department')
                ->groupBy('website_sliders.department_slider')
                ->get(),
            "websiteSliderList" => DB::table('website_sliders')
                ->join('website_details', 'website_details.id', 'website_sliders.website_id')
                ->leftJoin('inventory_general', 'inventory_general.id', 'website_sliders.prod_id')
                ->leftJoin('inventory_department', 'inventory_department.department_id', 'website_sliders.department_slider')
                ->select('website_sliders.id', 'website_sliders.website_id',
                 'website_sliders.slide',  'website_sliders.mobile_slide',
                 'website_sliders.invent_department_id','website_sliders.type as slider_type',
                 'website_sliders.invent_department_name', 'website_sliders.prod_id',
                 'inventory_general.department_id as prod_dept_id',
                  'inventory_general.sub_department_id as prod_subdept_id','website_sliders.department_slider','inventory_department.department_name as department_slider_name')
                ->where('website_details.company_id', $companyId)
                // ->where('website_sliders.type', 'default')
                ->where('website_sliders.status', 1)
                ->get()
        ]);
    }



    public function create_slider()
    {

        return view("websites.sliders.create", [
            //  "products"    => DB::table('inventory_general as inventGeneral')
            //                           ->join('website_products as webprod','webprod.inventory_id','inventGeneral.id')
            //                           ->where(['variations.company_id'=>Auth::user()->company_id,'variations.status'=>1])
            //                           ->select('prod_var_rel.id','prod_var_dtl.price','prod_var_dtl.image','variations.name as variat_name','posProducts.item_name as product_name','inventGeneral.product_name as parent_prod')
            //                           ->get();
            "websites"    => WebsiteDetail::where('company_id', Auth::user()->company_id)->get(),
            "departments" => InventoryDepartment::where('company_id', session('company_id'))->get()
        ]);
    }

    public function getDepart_n_subDepart_website_product(Request $request)
    {

        if (empty($request->website) && empty($request->mode)) {
            return 0;
        }


        if (WebsiteDetail::where('company_id', session('company_id'))->where('id', $request->website)->count() == 0) {
            return 0;
        }

        if ($request->mode == 'depart') {
            return  InventoryDepartment::whereIn('department_id', DB::table('inventory_general')->whereIn('id', WebsiteProduct::where('website_id', $request->website)->where('status', 1)->pluck('inventory_id'))->pluck('department_id'))->get();
        }

        if ($request->mode == 'subdepart') {
            return  DB::table('inventory_sub_department')->where('department_id', $request->depart)->get();
        }
    }

    public function getWebsite_prod(Request $request)
    {

        if (WebsiteDetail::where('company_id', session('company_id'))->where('id', $request->id)->count() == 0) {
            return 0;
        }

        return DB::table('website_products')
        ->join('inventory_general', 'inventory_general.id', 'website_products.inventory_id')
        ->where('website_products.website_id', $request->id)
        ->where('website_products.status', 1)
        ->when($request->department, function($query) use ($request) {
            return $query->where('inventory_general.department_id', $request->department);
        })
        ->when($request->subDepart, function($query) use ($request) {
            return $query->where('inventory_general.sub_department_id', $request->subDepart);
        })
        ->select('inventory_general.id', 'inventory_general.product_name')
        ->get();
        /* DB::table('website_products')
            ->join('inventory_general', 'inventory_general.id', 'website_products.inventory_id')
            ->where('website_products.website_id', $request->id)
            ->where('website_products.status', 1)
            ->where('inventory_general.sub_department_id', $request->subDepart)
            ->select('inventory_general.id', 'inventory_general.product_name')
            ->get();*/
    }


    public function store_slider(Request $request)
    {
        // dimensions:width=1520,height=460
        return $request;
       if(isset($request->slider_type) && \Hash::check('department', $request->slider_type)){
            $rules = [
                       'website_dept_slide'     => 'required',
                       'department_dpt_slide'   => 'required',
                       'desktop_slide_dept'     => 'required|mimes:jpg,jpeg,png,webp,mp4,webm,ogg|max:1024',
                       'mobile_slide_dept'      => 'nullable|mimes:jpg,jpeg,png,webp,mp4,webm,ogg|max:1024'
            ];
       }else{
            $rules = [
                       'website'       => 'required',
                       'desktop_slide' => 'required|mimes:jpg,jpeg,png,webp,mp4,webm,ogg|max:1024',
                       'mobile_slide'  => 'nullable|mimes:jpg,jpeg,png,webp,mp4,webm,ogg|max:1024'
            ];
       }

       $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect('website/slider/lists?#departmentSliderNav')->withErrors($validator)->withInput();
        }

        //$this->validate($request, $rules);

        $desktop_slide     = $request->file('desktop_slide') ?? $request->file('desktop_slide_dept');
        $imageName         = time() . '.' . strtolower($desktop_slide->getClientOriginalExtension());
        $mobile_slide      = $request->file('mobile_slide') ?? $request->file('mobile_slide_dept') ?? null;
        $mobile_slideName  = $mobile_slide == null ? null : 'mobile_size'.time() . '.' . strtolower($mobile_slide->getClientOriginalExtension());
        $productSlug       = null;
        $invent_department = null;

        $path = $this->create_folder('sliders/' . session('company_id'), $request->website);

        if ($path == false) {
            return response()->json('slider not uploaded.', 500);
        }

        if (!$desktop_slide->move($path, $imageName)) {
            return response()->json('slider not uploaded.', 500);
        }

        if($mobile_slide != null){
            if(!$mobile_slide->move($path, $mobile_slideName)){
                return response()->json('mobile slider not uploaded.', 500);
            }
        }

        if (!empty($request->product)) {
            $getprodSlug = DB::table('inventory_general')->where('id', '=', $request->product)->select('slug')->first();
            $productSlug = $getprodSlug->slug;
            $invent_department = null;
        }

        if (!empty($request->depart)) {
            $get_inventDepart = DB::table('inventory_department')->where('department_id', '=', $request->post('depart'))->select('department_name')->first();
            $invent_department = $get_inventDepart->department_name;

            $productSlug = null;
        }

        $result = DB::table('website_sliders')
            ->insertGetId([
                'website_id'             => isset($request->website) ? $request->website : $request->website_dept_slide,
                'invent_department_id'   => !empty($request->product) ? null : $request->post('depart'),
                'invent_department_name' => $invent_department,
                'prod_id'                => !empty($request->depart) ? null : $request->post('product'),
                'prod_slug'              => $productSlug,
                'slide'                  => $desktop_slide,
                'mobile_slide'           => $mobile_slideName,
                'status'                 => 1,
                'department_slider'      => $request->department_dpt_slide ?? null,
                'type'                   => isset($request->department_dpt_slide) ? 'department' : 'default'
            ]);

        if ($result) {

            if(isset($request->department_dpt_slide) && !empty($request->product_dpt_slide)){
                  foreach($request->product_dpt_slide as $value){
                          DB::table('website_slider_product_binds')
                              ->insert([
                                         'slider_id'  => $result,
                                         'product_id' => $value
                                       ]);
                  }
            }

            Session::flash('success', 'Success!');
        } else {

            Session::flash('error', 'Invalid record');
        }
        return redirect('website/slider/lists'.isset($request->department_dpt_slide) ? '?#departmentSliderNav' : null);
    }

    public function update_slide(Request $request)
    {

        $Slide        = $request->file('slide_md');
        $mobile_slide = $request->file('mobile_slide');
        $productSlug  = null;
        $departName   = null;

        $columnArray = ['updated_at' => date("Y-m-d H:i:s")];

        $get = DB::table('website_sliders')->where('id', '=', $request->id)->first();

        if ($Slide != '') {

            $rules = [
                'slide_md'   => 'required|mimes:jpg,jpeg,png,webp|max:1024'
            ];

            $this->validate($request, $rules);

            $imageName   = time() . '.' . $Slide->getClientOriginalExtension();

            $path = $this->create_folder('sliders/' . session('company_id'), $request->webId);

            if ($path == false) {
                Session::flash('error', 'Server Issue image not uploaded route issue.');
                return redirect()->route('sliderLists');
            }

            if (!$Slide->move($path, $imageName)) {
                Session::flash('error', 'Server Issue image not uploaded route issue.');
                return redirect()->route('sliderLists');
            }

            if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $request->webId . '/' . $get->slide)) {
                \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $request->webId . '/' . $get->slide);
            }
            $columnArray['slide'] = $imageName;
        }

        if ($mobile_slide != '') {

            $rules = [
                'mobile_slide'   => 'required|mimes:jpg,jpeg,png,webp|max:1024'
            ];

            $this->validate($request, $rules);

            $mobile_slideName   = 'mobile_size'.time() . '.' . $mobile_slide->getClientOriginalExtension();

            $path = $this->create_folder('sliders/' . session('company_id'), $request->webId);

            if ($path == false) {
                Session::flash('error', 'Server Issue image not uploaded route issue.');
                return redirect()->route('sliderLists');
            }

            if (!$mobile_slide->move($path, $mobile_slideName)) {
                Session::flash('error', 'Server Issue slide image not uploaded route issue.');
                return redirect()->route('sliderLists');
            }

            if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $request->webId . '/' . $get->mobile_slide)) {
                \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $request->webId . '/' . $get->mobile_slide);
            }
            $columnArray['mobile_slide'] = $mobile_slideName;
        }

        if (!empty($request->post('product_md'))) {
            $productSlug = DB::table('inventory_general')->where('id', '=', $request->post('product_md'))->select('slug')->first();
            $columnArray['prod_id']   = $request->post('product_md');
            $columnArray['prod_slug'] = $productSlug->slug;

            $columnArray['invent_department_id']   = null;
            $columnArray['invent_department_name'] = null;
        }

        if (!empty($request->post('depart_md'))) {
            $departName = DB::table('inventory_department')->where('department_id', '=', $request->post('depart_md'))->select('department_name')->first();
            $columnArray['invent_department_id']   = $request->post('depart_md');
            $columnArray['invent_department_name'] = $departName->department_name;

            $columnArray['prod_id']   = null;
            $columnArray['prod_slug'] = null;
        }




        //   return $columnArray;
        $result = DB::table('website_sliders')
            ->where('id', '=', $request->id)
            ->update($columnArray);

        if ($result) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Server Issue record not updated.');
        }

        return redirect()->route('sliderLists');
    }

    public function create_folder($comFOldName, $webFoldName)
    {
        $path   = 'storage/images/website/'. $comFOldName . '/' . $webFoldName;
        $result = true;
        if (!File::isDirectory($path)) {
            $result = File::makeDirectory($path, 0777, true, true);
        }

        return ($result) ? $path : false;
    }

    public function destroy_slide(Request $request)
    {

        if(isset($request->depart)){
           return$this->destroy_department_slide($request);
           die();
        }

        if (!isset($request->id)) {
            Session::flash('error', 'Server Issue invalid fields');
            return redirect()->route('sliderLists');
        }

        $id = $request->id;
        $path = 'storage/images/website/sliders/'. session('company_id') . '/' . $id . '/';

        if ($request->post('mode' . $id) == '') {
            $get = DB::table('website_sliders')->where('website_id', '=', $id)->get();

            if ($get != null) {
                foreach ($get as $val) {
                    $this->removeImage($path,$val->slide);
                    $this->removeImage($path,$val->mobile_slide);
                    // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->slide)) {
                    //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->slide);
                    // }

                    // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->mobile_slide)) {
                    //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->mobile_slide);
                    // }
                }

                $process = DB::table('website_sliders')->where('website_id', '=', $id)->delete();
            }
        } else {
            $get = DB::table('website_sliders')->where('id', '=', $request->post('mode' . $id))->first();
            if ($get != null) {
                $this->removeImage($path,$get->slide);
                $this->removeImage($path,$get->mobile_slide);
                // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->slide)) {
                //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->slide);
                // }

                // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->mobile_slide)) {
                //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->mobile_slide);
                // }

                $process = DB::table('website_sliders')->where('id', '=', $request->post('mode' . $id))->delete();
            }
        }

        if ($process) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Server Issue slide not removed.');
        }

        return redirect()->route('sliderLists');
    }

    public function destroy_department_slide(Request $request)
    {

        if (!isset($request->id) || !isset($request->depart)) {
            Session::flash('error', 'Server Issue invalid fields');
            return redirect('website/slider/lists?#departmentSliderNav');
        }

        $id = $request->id;
        $department_slider = $request->depart;
        $path = 'storage/images/website/sliders/'. session('company_id') . '/' . $id . '/';

        if ($request->post('mode' . $id) == '') {
            $get = DB::table('website_sliders')
                        ->where('department_slider', '=', $department_slider)
                        ->where('website_id', '=', $id)
                        ->get();

            if ($get != null) {
                foreach ($get as $val) {
                    $this->removeImage($path,$val->slide);
                    $this->removeImage($path,$val->mobile_slide);
                    // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->slide)) {
                    //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->slide);
                    // }

                    // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->mobile_slide)) {
                    //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $val->mobile_slide);
                    // }
                }

                $process = DB::table('website_sliders')
                            ->where('department_slider', '=', $department_slider)
                            ->where('website_id', '=', $id)
                            ->delete();
            }
        } else {
            $get = DB::table('website_sliders')->where('id', '=', $request->post('mode' . $id))->first();
            if ($get != null) {
                $this->removeImage($path,$get->slide);
                $this->removeImage($path,$get->mobile_slide);
                // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->slide)) {
                //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->slide);
                // }

                // if (\File::exists('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->mobile_slide)) {
                //     \File::delete('storage/images/website/sliders/'. session('company_id') . '/' . $id . '/' . $get->mobile_slide);
                // }

                $process = DB::table('website_sliders')->where('id', '=', $request->post('mode' . $id))->delete();
            }
        }

        if ($process) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Server Issue slide not removed.');
        }

        return redirect('website/slider/lists?#departmentSliderNav');
    }

    // ======================================================================================
    //                  Website Advertisement Modules
    //=======================================================================================

    public function getAdvertisement(Request $request)
    {
        $companyId = session('company_id');

        return view("websites.advertisements.index", [
            "websites"          => WebsiteDetail::where('company_id', $companyId)->get(),
            "departments"       => InventoryDepartment::where('company_id', $companyId)->get(),
            "websiteSlider"     => DB::table('website_advertisement_notifications as advertisement')
                ->join('website_details', 'website_details.id', 'advertisement.website_id')
                ->leftJoin('inventory_general', 'inventory_general.id', 'advertisement.prod_id')
                ->select('advertisement.*', 'website_details.name', 'inventory_general.department_id as prod_depart', 'inventory_general.sub_department_id as prod_sb_depart')
                ->where('website_details.company_id', $companyId)
                ->get(),
            // DB::table('website_advertisement_notifications as advertisement')
            //                         ->join('website_details', 'website_details.id', 'advertisement.website_id')
            //                         ->select('website_details.*')
            //                         ->where('website_details.company_id', Auth::user()->company_id)
            //                         ->groupBy('website_details.name')
            //                         ->get(),
            // "websiteSliderList" => DB::table('website_advertisement_notifications as advertisement')
            //     ->join('website_details', 'website_details.id', 'advertisement.website_id')
            //     ->select('advertisement.*')
            //     ->where('website_details.company_id', Auth::user()->company_id)
            //     ->get()
        ]);
    }



    public function create_Advertisement()
    {

        return view("websites.advertisements.create", [
            //  "products"    => DB::table('inventory_general as inventGeneral')
            //                           ->join('website_products as webprod','webprod.inventory_id','inventGeneral.id')
            //                           ->where(['variations.company_id'=>Auth::user()->company_id,'variations.status'=>1])
            //                           ->select('prod_var_rel.id','prod_var_dtl.price','prod_var_dtl.image','variations.name as variat_name','posProducts.item_name as product_name','inventGeneral.product_name as parent_prod')
            //                           ->get();
            "websites"    => WebsiteDetail::where('company_id', Auth::user()->company_id)->get(),
            "departments" => InventoryDepartment::where('company_id', Auth::user()->company_id)->where('status',1)->get()
        ]);
    }

    public function storeAdvertisement(Request $request)
    {

        $rules = [
            'website' => 'required',
            'image'   => 'required|mimes:jpg,jpeg,png|dimensions:width=576,height=576|max:1024'
        ];

        $this->validate($request, $rules);

        if (DB::table('website_advertisement_notifications')->where('website_id', $request->website)->count() > 0) {
            $rules = [
                'website' => 'required|unique:website_advertisement_notifications,website_id'
            ];

            $this->validate($request, $rules);

            // Session::flash('error','Another advertisement post already taken this website');
            // return redirect()->route('AdvertisementLists')->withInput($request->all())->withErrors();
        }


        $image             = $request->file('image');
        $imageName         = time() . '.' . $image->getClientOriginalExtension();
        $productSlug       = null;
        $invent_department = null;

        $path = $this->create_folder('advertisements/' . session('company_id'), $request->website);

        if ($path == false) {
            return response()->json('Image path is not defined.', 500);
        }

        if (!$image->move($path, $imageName)) {
            return response()->json('Image not uploaded.', 500);
        }

        if (!empty($request->product)) {
            $getprodSlug = DB::table('inventory_general')->where('id', '=', $request->product)->select('slug')->first();
            $productSlug = $getprodSlug->slug;

            $invent_department = null;
        }

        if (!empty($request->depart)) {
            $get_inventDepart = DB::table('inventory_department')->where('department_id', '=', $request->post('depart'))->select('department_name')->first();
            $invent_department = $get_inventDepart->department_name;

            $productSlug = null;
        }

        $result = DB::table('website_advertisement_notifications')
            ->insertGetId([
                'website_id'             => $request->website,
                'invent_department_id'   => !empty($request->product) ? null : $request->post('depart'),
                'invent_department_name' => $invent_department,
                'prod_id'                => !empty($request->depart) ? null : $request->post('product'),
                'prod_slug'              => $productSlug,
                'image'                  => $imageName,
                'status'                 => 1
            ]);

        if ($result) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Invalid record');
        }
        return redirect()->route('AdvertisementLists');
    }

    public function updateAdvertisement(Request $request)
    {

        $Slide = $request->file('image_md');
        $productSlug = null;
        $departName  = null;

        $columnArray = ['updated_at' => date("Y-m-d H:i:s")];

        $get = DB::table('website_advertisement_notifications')->where('id', '=', $request->id)->first();

        if ($Slide != '') {

            $rules = [
                'image_md'   => 'required|mimes:jpg,jpeg,png|dimensions:width=1520,height=460|max:1024'
            ];

            $this->validate($request, $rules);

            $imageName   = time() . '.' . $Slide->getClientOriginalExtension();

            $path = $this->create_folder('advertisements/' . session('company_id'), $request->webId);

            if ($path == false) {
                Session::flash('error', 'Server Issue image not uploaded route issue.');
                return redirect()->route('AdvertisementLists');
            }

            if (!$Slide->move($path, $imageName)) {
                Session::flash('error', 'Server Issue image not uploaded route issue.');
                return redirect()->route('AdvertisementLists');
            }

            if (\File::exists('storage/images/website/advertisement/'. session('company_id') . '/' . $request->webId . '/' . $get->image)) {
                \File::delete('storage/images/website/advertisement/'. session('company_id') . '/' . $request->webId . '/' . $get->image);
            }
            $columnArray['image'] = $imageName;
        }

        if (!empty($request->post('product_md'))) {
            $productSlug = DB::table('inventory_general')->where('id', '=', $request->post('product_md'))->select('slug')->first();
            $columnArray['prod_id']   = $request->post('product_md');
            $columnArray['prod_slug'] = $productSlug->slug;

            $columnArray['invent_department_id']   = null;
            $columnArray['invent_department_name'] = null;
        }

        if (!empty($request->post('depart_md'))) {
            $departName = DB::table('inventory_department')->where('department_id', '=', $request->post('depart_md'))->select('department_name')->first();
            $columnArray['invent_department_id']   = $request->post('depart_md');
            $columnArray['invent_department_name'] = $departName->department_name;

            $columnArray['prod_id']   = null;
            $columnArray['prod_slug'] = null;
        }




        //   return $columnArray;
        $result = DB::table('website_advertisement_notifications')
            ->where('id', '=', $request->id)
            ->update($columnArray);

        if ($result) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Server Issue record not updated.');
        }

        return redirect()->route('AdvertisementLists');
    }

    public function destroyAdvertisement(Request $request)
    {

        if (!isset($request->id)) {
            Session::flash('error', 'Server Issue invalid fields');
            return redirect()->route('AdvertisementLists');
        }

        $id = $request->id;

        // if($request->post('mode'.$id) == ''){
        $get = DB::table('website_advertisement_notifications')->where('website_id', $request->website)->where('id', $request->id)->get();

        if ($get != null) {
            foreach ($get as $val) {
                if (\File::exists('storage/images/website/advertisements/'. session('company_id') . '/' . $request->website . '/' . $val->image)) {
                    \File::delete('storage/images/website/advertisements/'. session('company_id') . '/' . $request->website . '/' . $val->image);
                }
            }

            $process = DB::table('website_advertisement_notifications')->where('website_id', $request->website)->where('id', $request->id)->delete();
        }

        // }else{
        //     $get = DB::table('website_advertisement_notifications')->where('id','=',$request->post('mode'.$id))->first();
        //     if($get != null){

        //       if(\File::exists(public_path('storage/images/website/advertisements/').session('company_id').'/'.$id.'/'.$get->image)){
        //           \File::delete(public_path('storage/images/website/advertisements/').session('company_id').'/'.$id.'/'.$get->image);
        //       }

        //       $process = DB::table('website_advertisement_notifications')->where('id','=',$request->post('mode'.$id))->delete();
        //     }

        // }

        if ($process) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Server Issue slide not removed.');
        }

        return redirect()->route('AdvertisementLists');
    }




    public function getSocialLink(Request $request)
    {
        $companyId = session('company_id');

        $socialFullNameArray = ['fb' => 'FaceBook', 'youtube' => 'Youtube', 'insta' => 'Instagram', 'linkedin' => 'Linkedin', 'twite' => 'Twitter', 'tiktok' => 'TikTok', 'snapchat' => 'Snapchat', 'pinterest' => 'Pinterest'];

        return view("websites.social-link.index", [
            "websites"       => WebsiteDetail::where('company_id', $companyId)->get(),
            "lists"          => DB::table('website_social_connects as sociaLinks')
                ->join('website_details', 'website_details.id', 'sociaLinks.website_id')
                ->whereIn('sociaLinks.website_id', WebsiteDetail::where('company_id', '=', $companyId)->pluck('id'))
                ->select('sociaLinks.website_id', 'website_details.name')
                ->groupBy('sociaLinks.website_id')
                ->get(),
            "sublists"          => DB::table('website_social_connects as sociaLinks')
                ->join('website_details', 'website_details.id', 'sociaLinks.website_id')
                ->whereIn('sociaLinks.website_id', WebsiteDetail::where('company_id', '=', $companyId)->pluck('id'))
                ->select('sociaLinks.*', 'website_details.name')
                ->get(),
            "socialFullName"  => $socialFullNameArray

        ]);
    }

    public function store_SocialLink(Request $request)
    {
        $rules = [
            'website'     => 'required',
            'socialType'  => 'required',
            'url'         => 'required',
        ];

        $this->validate($request, $rules);

        $iconArray = [
            'fb'        => 'icofont icofont-social-facebook',
            'insta'     => 'icofont icofont-social-instagram',
            'youtube'   => 'icofont icofont-social-youtube',
            'linkedin'  => 'fa-brands fa-linkedin',
            'twite'     => 'fa-brands fa-twitter',
            'tiktok'    => 'fa-brands fa-tiktok',
            'snapchat'  => 'icofont icofont-social-snapchat',
            'pinterest' => 'icofont icofont-social-pinterest'
        ];

        if (DB::table('website_social_connects')->where(['website_id' => $request->website, 'social_type' => $request->socialType])->count() > 0) {
            return redirect()->route('socialList')->with('socialType', 'this social account already exists')->withInput();
        }

        $result = DB::table('website_social_connects')
            ->insert([
                'website_id'   => $request->website,
                'social_type'  => $request->socialType,
                'icon'         => $iconArray[$request->socialType],
                'url'          => $request->url,
                'status'       => 1,
                'created_at'   => date("Y-m-d H:i:s")
            ]);

        if ($result) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Error! record is not submit');
        }

        return redirect()->route('socialList');
    }

    public function update_socialLink(Request $request)
    {
        if (empty($request->id) && empty($request->type)) {
            Session::flash('error', 'Server issue invalid fields.');
            return redirect()->route('socialList');
        }

        if (DB::table('website_social_connects')->where(['id' => $request->id, 'social_type' => $request->type])->update(['url' => $request->value])) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Server issue record not updated.');
        }

        return redirect()->route('socialList');
    }



    public function destroy_socialLink(Request $request, $id)
    {
        if (empty($id)) {
            Session::flash('error', 'Server issue record is not removed.');
            return redirect()->route('socialList');
        }

        if (isset($request->mode)) {
            if (DB::table('website_social_connects')->where(['id' => $id])->delete()) {
                Session::flash('success', 'Successfully');
            } else {
                Session::flash('error', 'Error Not Removed.');
            }
        } else {
            if (DB::table('website_social_connects')->where(['website_id' => $id])->delete()) {
                Session::flash('success', 'Successfully');
            } else {
                Session::flash('error', 'Error Not Removed.');
            }
        }

        return redirect()->route('socialList');
    }


    // ======================================================================================
    //                  Website Delivery areas
    //=======================================================================================

    public function getDeliveryArea(Request $request, branch $branch)
    {

        $companyId = session('company_id');

        $deliveryAreaLists = DB::table('website_delivery_areas as AreaList')
            ->join('branch', 'branch.branch_id', 'AreaList.branch_id')
            ->join('website_details as website', 'website.id', 'AreaList.website_id')
            ->select('AreaList.*', 'branch.branch_name', 'website.name as website_name')
            // ->whereIn('branch.branch_id',DB::table('branch')->where('company_id','=',$companyId)->pluck('branch_id'))
            ->whereIn('AreaList.website_id', WebsiteDetail::where(['company_id' => $companyId, 'status' => 1])->pluck('id'))
            ->where('AreaList.remove', '=', 0)
            ->groupBy('AreaList.website_id')
            ->get();

        $deliveryAreaValues = DB::table('website_delivery_areas as AreaList')
                                ->leftJoin('city', 'city.city_id', '=', 'AreaList.city_id')
                                ->whereIn('AreaList.website_id', WebsiteDetail::where(['company_id' => $companyId, 'status' => 1])->pluck('id'))
                                ->where('remove', '=', 0)
                                ->select('AreaList.*', 'city.city_name')
                                ->orderByRaw('city.city_name IS NULL, city.city_name ASC')
                                ->get();

        // DB::table('website_delivery_areas as AreaList')
        //     ->leftJoin('city', 'city.city_id', 'AreaList.city_id')
        //     // ->whereIn('branch_id',DB::table('branch')->where('company_id','=',$companyId)->pluck('branch_id'))
        //     ->whereIn('AreaList.website_id', WebsiteDetail::where(['company_id' => $companyId, 'status' => 1])->pluck('id'))
        //     ->where('remove', '=', 0)
        //     ->select('AreaList.*', 'city.city_name')
        //     ->get();

        return view("websites.delivery-area.index", [
            "website"            => WebsiteDetail::where(['company_id' => $companyId, 'status' => 1])->get(),
            // "branch"             => DB::table('branch')->where(['company_id'=>$companyId,'status_id'=>1])->get(),
            "city"               => DB::table('city')->where('country_id', 170)->get(),
            "deliveryList"       => $deliveryAreaLists,
            "deliveryAreaValue"  => $deliveryAreaValues,
        ]);
    }

    public function cityLoadnotExistsdilveryArea(Request $request){

        if(isset($request->branchCode) && isset($request->websiteCode) && $request->mode == 1){
            $cities = DB::table('city')
            ->whereIn('city_id', function($query) use ($request) {
                $query->select('city')
                    ->from('website_delivery_areas')
                    ->where('website_id', $request->websiteCode)
                    ->where('branch_id', $request->branchCode)
                    ->where('remove','=', 0);
            })
            ->pluck('city_id');

        $result = DB::table('city')
            ->whereNotIn('city_id', $cities)
            ->where('country_id',170)
            ->orderBy('city_name','ASC')
            ->get();


        //    $result =   DB::table('website_delivery_areas')
        //                     ->leftJoin('city', 'city.city_id', '!=', 'website_delivery_areas.city')
        //                     ->where('website_delivery_areas.website_id', '=', $request->websiteCode)
        //                     ->where('website_delivery_areas.branch_id', '=', $request->branchCode)
        //                     ->where('website_delivery_areas.status', '=', 1)
        //                     // ->where('city.city_id', '!=', 'website_delivery_areas.city') // Ensure cities are not equal
        //                     ->select('website_delivery_areas.city', 'city.city_name')
        //                     ->get();
           return response()->json($result,200);
        }

        if($request->mode == 0){
           $result = DB::table('city')->where('country_id', 170)->get();
           return response()->json($result,200);
        }

      return response()->json('Record not found!',500);
    }

    public function getDeliveryAreaValues(Request $request)
    {
        $companyId = session('company_id');

        return DB::table('website_delivery_areas')
            ->join('city', 'city.city_id', 'website_delivery_areas.city_id')
            ->where('website_delivery_areas.website_id', '=', $request->website)
            ->where('website_delivery_areas.remove','=',0)
            ->select('website_delivery_areas.*', 'city.city_name')
            ->orderBy('city.city_name','ASC')
            ->get();

        // return DB::table('website_delivery_areas')
        //           ->leftJoin('city','city.city_id','website_delivery_areas.city')
        //           ->whereIn('branch_id', DB::table('website_delivery_areas as AreaList')
        //                 ->join('website_details', 'website_details.id', 'AreaList.website_id')
        //                 ->join('branch', 'branch.branch_id', 'AreaList.branch_id')
        //                 ->select('AreaList.branch_id')
        //                 ->where('website_details.company_id', '=', $companyId)
        //                 ->where('AreaList.status', '=', 1)
        //                 ->pluck('AreaList.branch_id'))
        //           ->where('branch_id','=',$request->branchId)
        //           ->select('website_delivery_areas.*','city.city_name')
        //           ->get();
    }

    public function getWebsiteBranches(Request $request, branch $branch)
    {
        return response()->json($branch->getWebsiteBranches($request->websiteId));
    }

    public function store_deliveryArea(Request $request)
    {
       try {
             DB::beginTransaction();
        // $rules = [
        //     'branch'    => 'required',
        //     'areas'     => 'required',
        //     'charges'   => 'required',
        // ];

        // $this->validate($request, $rules);

        // $result = null;

        if (!isset($request->on_off_btn)) {
            $city  = $request->city;

            for ($i = 0; $i < count($city); $i++) {

               $getCity= DB::table('city')->where('city_id', $city[$i])->first();
                $result = DB::table('website_delivery_areas')
                    ->insert([
                        'website_id'         => $request->website,
                        'branch_id'          => $request->branch,
                        'name'               => $getCity->city_name,
                        'city_id'            => addslashes($city[$i]),
                        'is_city'            => 1,
                        'estimate_of_days'   => $request->estimate_day,
                        'charge'             => $request->charges,
                        'min_order'          => $request->min_order == '' ? 0 : $request->min_order,
                        'min_order'          => $request->delivery_free_on_min_order == '' ? 0 : $request->delivery_free_on_min_order,
                        'status'             => 1,
                    ]);
            }
        } else {
            $areas  = explode(',', $request->areas);

            for ($i = 0; $i < count($areas); $i++) {
                  DB::table('website_delivery_areas')
                    ->insert([
                        'website_id'         => $request->website,
                        'branch_id'          => $request->branch,
                        'name'               => addslashes($areas[$i]),
                        'city_id'            => $request->city,
                        // 'city'               => $request->city,
                        'estimate_time'      => $request->time_estimate,
                        'estimate_of_days'   => $request->estimate_day,
                        'charge'             => $request->charges,
                        'min_order'          => $request->min_order == '' ? 0 : $request->min_order,
                        'status'             => 1,
                        // 'is_city'            => session('company_id') == 102 ? 1 : 0,
                    ]);
            }
        }

        DB::commit();
        // if ($result == null) {
        //     Session::flash('error', 'Record is not created!');
        // } else {
            Session::flash('success', 'Success!');
        // }
        }catch(\Exception $e){
            DB::rollback();
            Session::flash('error', 'Error! '.$e->getMessage());
        }

        return redirect()->route('deliveryAreasList');
        // return $result == null ? response()->json('Error! Record is not created!', 500) : response()->json('Success!', 200);
    }

    public function single_deliveryAreaName_store(Request $request)
    {

        $branchId       = $request->branchId;
        $branchName     = $request->branchName;
        $websiteId      = $request->websiteId;
        $websiteName    = $request->websiteName;
        $city           = $request->city;
        $areaName       = $request->areaName_md;

        if ($request->iscity == 1) {
            if (DB::table('website_delivery_areas as areasList')->where(['branch_id' => $branchId, 'city' => $city, 'website_id' => $websiteId])->count() > 0) {
                return response()->json(['error' => 'This city already taken this branch ' . $branchName, 'control' => 'city_md']);
            }
        } else {
            if (DB::table('website_delivery_areas')->where(['branch_id' => $branchId, 'name' => $areaName, 'website_id' => $websiteId])->count() > 0) {
                return response()->json(['error' => 'This ' . $areaName . ' area already taken this branch ' . $branchName, 'control' => 'areaName_md']);
            }
        }

        $getRecord = DB::table('website_delivery_areas')
            ->where('branch_id', '=', $branchId)
            ->first();

        if ($getRecord == null) {
            return response()->json(['error' => 'Server Issue']);
        }

        $columnInsert = [
            'branch_id'       => $getRecord->branch_id,
            'website_id'      => $getRecord->website_id,
            'charge'          => $request->charges_md,
            'min_order'       => $request->min_order_md,
            'status'          => $getRecord->status,
        ];

        if ($request->iscity == 1) {
            $columnInsert['city']                = $city;
            $columnInsert['is_city']             = 1;
            $columnInsert['estimate_of_days']    = empty($request->estimate_md) ? $getRecord->estimate_of_days : $request->estimate_md;
        } else {
            $columnInsert['city_id']          = $getRecord->city_id;
            $columnInsert['name']             = addslashes($areaName);
            $columnInsert['estimate_time']    = empty($request->estimate_md) ? $getRecord->estimate_time : $request->estimate_md;
        }

        $resp = DB::table('website_delivery_areas')
            ->insert($columnInsert);


        return $resp ? response()->json('success', 200) : response()->json(['error' => 'Record is not submited Server issue please try again later']);
    }

    public function update_deliveryAreaSpecificField(Request $request)
    {

        if (empty($request->branchid)) {
            Session::flash('error', 'Invalid fields');
            return redirect()->route('deliveryAreasList');
        }


        $columnNames_array = ['status', 'min_order', 'charge'];
        $get_columnName = null;
        $value          = null;

        // $websiteId  = $request->webid;
        $branchId   = $request->branchid;

        foreach (collect($request->all())->keys() as $key) {
            if (in_array($key, $columnNames_array)) {
                $get_columnName = $key;
                $value          = $request->post($get_columnName);
                break;
            }
        }


        if (!in_array($get_columnName, $columnNames_array) || empty($branchId)) {
            Session::flash('error', 'Empty fields');
            return redirect()->route('deliveryAreasList');
        }

        if (DB::table('website_delivery_areas')->where(['branch_id' => $branchId])->update([$get_columnName => $value])) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Server issue record is not updated');
        }

        return redirect()->route('deliveryAreasList');
    }

    public function update_deliveryArea(Request $request)
    {

        $uniqueId    = $request->id;
        $areaName    = $request->area;
        $charge      = $request->charge;
        //      $websiteId   = $request->webId;
        //      $websiteName = $request->webName;

        if (empty($uniqueId)) {
            return response()->json(['status' => 500, 'msg' => 'Invalid field']);
        }

        //      if (DB::table('website_delivery_areas')->where(['website_id' => $websiteId, 'name' => $areaName])->where('id', '!=', $uniqueId)->count() > 0) {

        //          return response()->json('This ' . $areaName . ' area name already taken this ' . $websiteName . ' wsebsite.',);
        //      }

        $column = [];
        $column['charge']     =$charge;
        $column['updated_at'] =now();
        if($request->mode != 1){
          $column['name'] = $areaName;
        }

        if (DB::table('website_delivery_areas')->where(['id' => $uniqueId])->update($column)) {
            return response()->json(['status' => 200]);
        } else {
            return response()->json(['status' => 500, 'msg' => 'Server issue record is not updated.']);
        }
    }

    public function destroy_deliveryArea(Request $request)
    {

        if (empty($request->branchid)) {
            Session::flash('error', 'Server issue record is not removed.');
            return redirect()->route('deliveryAreasList');
        }

        if (DB::table('website_delivery_areas')->where('branch_id', '=', $request->branchid)->update(['remove' => 1])) {
            Session::flash('success', 'Successfully');
        } else {
            Session::flash('error', 'This ' . $request->branchName . ' branch delivery area remove for this ' . $request->websiteName . ' website.');
        }

        return redirect()->route('deliveryAreasList');
    }

    public function destroy_deliveryAreaValue(Request $request)
    {

        if (empty($request->id) || empty($request->branchid)) {

            // if (!isset($request->stp_redirect)) {
                return response()->json('Invalid field',500);
            // }
            // Session::flash('error', 'Server issue record is not removed.');
            // return redirect()->route('deliveryAreasList');
        }

        if (DB::table('website_delivery_areas')->where(['id' => $request->id, 'branch_id' => $request->branchid])->update(['remove' => 1])) {

            // if (isset($request->stp_redirect)) {
                return response()->json('success',200);
            // }
            // Session::flash('success', 'Successfully');
        } else {

            // if (isset($request->stp_redirect)) {
                return response()->json('This ' . $request->area . ' delivery area not remove.',500);
            // }

            // Session::flash('error', 'This ' . $request->areaName . ' delivery area not remove for this ' . $request->branchName . ' branch.');
        }

        // return redirect()->route('deliveryAreasList');
    }

    public function getTerminalAssign(Request $request, branch $branch)
    {
        // $terminalAssign = [];

        // if ($request->id != "") {
        //     $terminalAssign = DB::table("website_branches")
        //                      ->join("branch","branch.branch_id","=","website_branches.branch_id")
        //                      ->join("terminal_details","terminal_details.terminal_id","=","website_branches.terminal_id")
        //                      ->join("website_details","website_details.id","=","website_branches.website_id")
        //                      ->select("website_branches.id","website_details.id as website_id","website_details.name","branch.branch_id","branch.branch_name","terminal_details.terminal_id","terminal_details.terminal_name")
        //                      ->where("website_branches.id",$request->id)
        //                      ->get();
        // }
        // return $this->getAllTerminalAssign();
        return view("websites.terminal-assign.index", [
            "websites" => WebsiteDetail::where('company_id', Auth::user()->company_id)->get(),
            // "terminalAssign" => (!empty($terminalAssign) != "" ? $terminalAssign[0] : []),
            "terminalAssigns" => $this->getAllTerminalAssign(),
        ]);
    }

    public function getTerminalsFromBranches(Request $request, Terminal $terminal)
    {
        return response()->json($terminal->getterminals($request->branchId));
    }

    public function storeterminalBind(Request $request)
    {
        $this->validate($request, [
            "website"  => "required",
            "branch"   => "required",
            "terminal" => "required",
        ]);

        if (DB::table('website_branches')->where(['website_id' => $request->website, 'branch_id' => $request->branch, 'terminal_id' => $request->terminal, 'status' => 1])->count() > 0) {
            return redirect()->route('terminalAssignList')->withInput($request->input())->withErrors('terminal', 'This terminal already taken');
        }

        // if ($request->id == "") {
        $result = DB::table('website_branches')
            ->insert([
                'website_id'  => $request->website,
                'branch_id'   => $request->branch,
                'is_open'     => isset($request->is_open) ? 1 : 0,
                'terminal_id' => $request->terminal,
                'user_id'     => auth()->user()->id,
                'created_at'  => date("Y-m-d H:i:s"),
            ]);

        if ($result) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Error! Server Issue');
        }

        // } else {
        //     DB::table('website_branches')
        //         ->where("id", $request->id)
        //         ->update([
        //             'website_id'  => $request->website,
        //             'branch_id'   => $request->branch,
        //             'terminal_id' => $request->terminal,
        //             'updated_at'  => date("Y-m-d H:i:s"),
        //         ]);
        // }
        return redirect()->route("terminalAssignList");
    }

    public function updateTerminalBind(Request $request)
    {

        if ($request->mode == 1) {
            DB::table('website_branches')
                ->where("id", $request->id)
                ->update(['is_open' => $request->is_open]);
            return response()->json(['status' => 200]);
            exist();
        }

        //   if(!empty($request->oldTerminal) && $request->oldTerminal != $request->terminal_md){
        if (DB::table('website_branches')->where(['website_id' => $request->website_md, 'branch_id' => $request->branch_md, 'terminal_id' => $request->terminal_md, 'status' => 1])->where('id', '!=', $request->id)->count() > 0) {
            return response()->json(['status' => 500, 'msg' => 'This terminal already taken', 'control' => 'terminal']);
        }
        //   }

        $result = DB::table('website_branches')
            ->insert([
                'website_id'  => $request->website_md,
                'branch_id'   => $request->branch_md,
                'is_open'     => isset($request->is_open_md) ? 1 : 0,
                'terminal_id' => $request->terminal_md,
                'user_id'     => auth()->user()->id,
                'created_at'  => date("Y-m-d H:i:s"),
                'status'      => 1,
            ]);

        if ($result) {
            $getPreviousRecord = DB::table('website_branches')
                ->where(['website_id' => $request->website_md, 'branch_id' => $request->branch_md, 'id' => $request->id, 'status' => 1])
                ->first();

            if ($getPreviousRecord) {
                DB::table('website_branches')
                    ->where(['website_id' => $request->website_md, 'branch_id' => $request->branch_md, 'id' => $request->id])
                    ->update(['status' => 0]);
            }
        }

        return ($result) ? response()->json(['status' => 200]) : response()->json(['status' => 500, 'msg' => 'Error! Server Issue']);
    }

    public function deleteTerminalBind(Request $request)
    {
        if ($request->id != "") {
            DB::table('website_branches')->where("id", $request->id)->update(['status' => 0]);
            return response()->json(["status" => 200, "message" => "Terminal deleted successfully"]);
        }
        return response()->json(["status" => 500, "message" => "Some Error Occurred"]);
    }

    public function getAllTerminalAssign()
    {
        return DB::table("website_branches")
            ->join("branch", "branch.branch_id", "=", "website_branches.branch_id")
            ->join("terminal_details", "terminal_details.terminal_id", "=", "website_branches.terminal_id")
            ->join("website_details", "website_details.id", "=", "website_branches.website_id")
            ->whereIn("website_branches.branch_id", DB::table("branch")->where("company_id", auth()->user()->company_id)->pluck("branch_id"))
            ->where('website_branches.status', 1)
            ->select("website_branches.id", "website_details.id as website_id", "website_details.name", "website_branches.branch_id", "branch.branch_name", "terminal_details.terminal_name", "terminal_details.terminal_id", "website_branches.is_open")
            ->get();
    }

    public function viewBranchTiming(Request $request, branch $branch)
    {
        return view("websites.branch-timing.index", [
            "websites" => WebsiteDetail::where('company_id', Auth::user()->company_id)->get(),
            "days" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
        ]);
    }

    public function storeBranchTiming(Request $request)
    {

        // UPDATE THE RECORD
        if ($request->mode == "update") {
            for ($i = 0; $i < count($request->id); $i++) {
                DB::table("website_branches_schedule")
                    ->where("id", $request->id[$i])
                    ->update([
                        "opening_time" => date("H:i", strtotime($request->starttime[$i])),
                        "closing_time" => date("H:i", strtotime($request->endtime[$i])),
                    ]);
            }
        } else {
            if (count($request->dayname) > 0) {
                for ($i = 0; $i < count($request->dayname); $i++) {
                    DB::table("website_branches_schedule")
                        ->insert([
                            "branch_id"    => $request->branch_id,
                            "day"          => $request->dayname[$i],
                            "opening_time" => date("H:i", strtotime($request->starttime[$i])),
                            "closing_time" => date("H:i", strtotime($request->endtime[$i])),
                        ]);
                }
            }
        }
        return redirect()->route("branchTimingList");
        // foreach ($request->dayname as $key => $value) {
        //  echo substr($value, 0, 3);
        // }

        // foreach ($request->starttime as $key => $value) {
        //  echo date("H:i:s",strtotime( $value));
        // }
    }

    public function getBranchTiming(Request $request)
    {
        return view("websites.branch-timing.branch-table", [
            "days" => ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            "websiteId" => $request->websiteId,
            "branchId"  => $request->branchId,
            "timings"   => DB::table("website_branches_schedule")->where("branch_id", $request->branchId)->get(),
        ]);
    }


    public function website_setting(Request $request)
    {

        $companyId  = session('company_id');
        $GetWebsite = null;
        $webId      = null;

        if (isset($request->id)) {
            $webId = $request->id;
            $GetWebsite = WebsiteDetail::join('website_theme', 'website_theme.website_id', 'website_details.id')
                ->where(['website_details.id' => $webId, 'website_details.company_id' => $companyId])
                ->select(
                    'website_details.*',
                    'website_theme.fontstyle',
                    'website_theme.checkout_otp',
                    'website_theme.cart_layout',
                    'website_theme.product_view',
                    'website_theme.location_modal',
                    'website_theme.back_to_top_btn',
                    'website_theme.product_list',
                    'website_theme.depart_nav_layout',
                    'website_theme.top_contact_box',
                    'website_theme.footer_layout',
                    'website_theme.otp_whatsapp_msg',
                    'website_theme.otp_msg',
                    'website_theme.advertisement',
                    'website_theme.js_script'
                )
                ->first();
        }

        return view(
            'websites.setting.index',
            [
                "websiteLists" => WebsiteDetail::where('company_id', $companyId)->get(),
                "GetWebsite"   => $GetWebsite,
                "webId"        => $webId,
            ]
        );
    }

    public function get_websiteBranches_schedule(Request $request)
    {

        return DB::table('website_branches')->join('branch', 'branch.branch_id', 'website_branches.branch_id')
            ->where('website_branches.website_id', '=', $request->id)
            ->select('website_branches.*', 'branch.branch_name')
            ->groupBy('website_branches.branch_id')
            ->get();
    }

    public function websiteBranches_isOpen(Request $request)
    {

        $result = DB::table('website_branches')->where('id', '=', $request->id)->update(['is_open' => $request->value]);

        if (
            DB::table('website_branches')
            ->join('website_details', 'website_details.id', 'website_branches.website_id')
            ->where('website_branches.website_id', '=', $request->website)
            ->where('website_branches.is_open', '=', 1)
            ->where('website_details.company_id', '=', session('company_id'))
            ->count() > 0
        ) {

            DB::table('website_details')->where('id', '=', $request->website)->update(['is_open' => 1]);
        } else {
            DB::table('website_details')->where('id', '=', $request->website)->update(['is_open' => 0]);
        }

        return $result;
    }

    public function websiteIsOpen(Request $request)
    {

        return DB::table('website_branches')
            ->join('website_details', 'website_details.id', 'website_branches.website_id')
            ->where('website_branches.website_id', '=', $request->website)
            ->where('website_branches.is_open', '=', 1)
            ->where('website_details.company_id', '=', session('company_id'))
            ->count();
    }

    public function webSetting_saveChanges(Request $request)
    {

        if (empty($request->id)) {
            return response()->json('Invalid fields');
        }

        $columnNames_array = ['is_open', 'maintenance_mode', 'logo', 'favicon', 'fontstyle', 'checkout_otp', 'topbar', 'topbar_slide_msg', 'advertisement'];
        $websiteId  = $request->id;
        $companyId  = session('company_id');
        $value = $request->val;

        $getRecord_webTheme  = DB::table('website_theme')->where('website_id', '=', $websiteId)->first();
        $getRecord_webDetail = DB::table('website_details')->where(['id' => $websiteId, 'company_id' => $companyId])->first();

        if ($getRecord_webDetail == null) {
            return response()->json('Invalid fields');
        }

        if ($request->mode == 'theme') {
            $getRecord_webTheme = (array) $getRecord_webTheme;

            // if ($request->col == 'js_script') {
            //     //  $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            //     $value = $value;
            //     if (htmlspecialchars($getRecord_webTheme[$request->col], ENT_QUOTES) == htmlspecialchars($value, ENT_QUOTES)) {
            //         return response()->json('success');
            //     }
            // } else {

                if ($getRecord_webTheme[$request->col] == $value) {
                    return response()->json('success');
                }

                $value = addslashes($value);
            // }

            if (DB::table('website_theme')->where('website_id', '=', $websiteId)->update([$request->col => $value])) {

                return response()->json('success');
            } else {
                return response()->json('error');
            }
        } else {
            $getRecord_webDetail = (array) $getRecord_webDetail;

            if (in_array($request->col, ['logo', 'favicon', 'dark_logo'])) {

                $rules = ['logo' => 'mimes:jpeg,png,jpg,webp|max:1024'];
                // Create the validator instance
                $validator = Validator::make($request->all(), $rules);

                // Check if validation fails
                if ($validator->fails()) {
                    // Redirect back with input and errors
                    return response()->json('error'.$validator);
                }

                // $websiteName  = strtolower(str_replace(array(" ", "'"), '-', $getRecord_webDetail['name']));

                // $image = $request->file('value');

                // $imageName = $websiteName . '-' . $request->col . '.' . $image->getClientOriginalExtension();

                // $imglogo = Image::make($request->value)->resize(150, 70);
                // $image->move(public_path('storage/images/website'), $imageName);

                $getFile = $this->uploads($request->file('value'),'images/website/',$getRecord_webDetail[$request->col]);

                $value = !empty($getFile) ? $getFile['fileName'] : '';

                // $value = $imageName;
            }else if($request->col == 'topbar_slide_msg'){

                //$value = (array) $request->val;
                // $arrayValue = [];
                // array_push($arrayValue,$request->val);
                $value = json_encode($request->val);

            } else {

                if ($getRecord_webDetail[$request->col] == $request->val) {
                    return response()->json('success');
                }
            }


            if (DB::table('website_details')->where(['id' => $websiteId, 'company_id' => $companyId])->update([$request->col => ($value == '' ? null : $value)])) {

                if ($request->col == 'is_open') {
                    DB::table('website_branches')->where('website_id', '=', $websiteId)->update(['is_open' => 0]);
                }

                return response()->json('success');
            } else {
                return response()->json('error');
            }
        }
    }


    // =============================================//
    //            Customer Review Section
    // =============================================//


    public function getCustomer_reviews(Request $request){
        $data = [];
        if(isset($request->id)){
            $data["websiteId"] = $request->id;
            $data["reviews"] = DB::table('website_customer_reviews')
                                        ->join('website_details','website_details.id','website_customer_reviews.website_id')
                                        ->where('website_customer_reviews.website_id',$request->id)
                                        ->where('website_customer_reviews.status','!=',99)
                                        ->where('website_details.status','=',1)
                                        ->where('website_details.company_id',session('company_id'))
                                        ->select('website_customer_reviews.*')
                                        ->orderBy('website_customer_reviews.id','DESC')
                                        ->get();
            $data["images"] = DB::table('website_customer_review_images')
                                        ->join('website_customer_reviews','website_customer_reviews.id','website_customer_review_images.review_id')
                                        ->join('website_details','website_details.id','website_customer_reviews.website_id')
                                        ->where('website_customer_reviews.website_id',$request->id)
                                        ->where('website_customer_reviews.status','!=',99)
                                        ->where('website_details.status','=',1)
                                        ->where('website_details.company_id',session('company_id'))
                                        ->select('website_customer_review_images.*')
                                        ->get();

        }

        $data["websites"] = WebsiteDetail::where('company_id',session('company_id'))->where('status',1)->get();
        return view('websites.customer-review.index',$data);
    }

    // public function Customer_review_approved(Request $request){
    //     if(isset($request->mode) && isset($request->website)){
    //         return DB::table('website_customer_reviews')
    //                 ->where('website_id',$request->website)
    //                 ->update(['status'=>$request->mode]) ? response()->json('Success!',200) : response()->json('Error! customer review is not approved. Server Issue!',500);
    //     }
    //    return response('Record not found!',500);
    // }

    public function activeInactiveCustomer_review(Request $request){
        $websiteId = Crypt::decrypt($request->website);
        $id        = Crypt::decrypt($request->id);
        $getstCode = Crypt::decrypt($request->stcode);
        $getRecord = DB::table('website_customer_reviews')->where('id',$id)->where('website_id',$websiteId)->first();

        $status = $getstCode == 1 ? 'Active' : 'In-Active';
        $stCode = $getstCode == 1 ? 0 : 1;

        if ($getRecord == null) {
            Session::flash('error', 'Error! record not found! Server Issue!');
            return redirect()->route("filterCustomerReviews",$websiteId);
        }

        if (DB::table('website_customer_reviews')->where('id',$id)->where('website_id',$websiteId)->update(['status'=>$stCode,'updated_at'=>now()])) {
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Error! this ' . $getRecord->customer_name . ' review is not been '.$status.' from '.$request->websiteName.' website!');
        }
        return redirect()->route("filterCustomerReviews",$websiteId);
    }

    public function destroyCustomer_review(Request $request,$id){
        $websiteId = Crypt::decrypt($request->website);
        $id        = Crypt::decrypt($request->id);
        $getRecord = DB::table('website_customer_reviews')->where('id',$id)->where('website_id',$websiteId)->first();

        if ($getRecord == null) {
            Session::flash('error', 'Error! record not found! Server Issue!');
            return redirect()->route("filterCustomerReviews",$websiteId);
        }

        if (DB::table('website_customer_reviews')->where('id',$id)->where('website_id',$websiteId)->update(['status'=>99,'updated_at'=>now()])) {
            // $this->removeImage('/images/customer-reviews/',$getRecord->image);
            Session::flash('success', 'Success!');
        } else {
            Session::flash('error', 'Error! this ' . $getRecord->customer_name . ' review is not removed from '.$request->websiteName.' website !');
        }
        return redirect()->route("filterCustomerReviews",$websiteId);
    }

}
