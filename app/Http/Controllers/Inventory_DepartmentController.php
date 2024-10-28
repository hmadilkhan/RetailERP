<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\custom_helper;
use App\inventory_department;
use App\Traits\MediaTrait;
use App\Models\Inventory;
use App\WebsiteProduct;
use App\Section;
use Auth, File, Image, Session;

class Inventory_DepartmentController extends Controller
{
    use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if(Auth::user()->username == 'demoadmin'){
        //     return inventory_department::with('inventoryDepartmentSection')->get();
        // }
        // return WebsiteProduct::whereIn("inventory_id",Inventory::where("department_id",1229)->where("status",1)->pluck("id"))->groupBy("website_id")->pluck("website_id");
        $depart = inventory_department::with(['inventoryDepartmentSection:id,department_id,section_id'])
            ->where('status', 1)
            ->where('company_id', session('company_id'))
            ->with(['websiteProducts' => function ($query) {
                $query->select('website_id', 'inventory_id')->where("website_products.status", 1)->groupBy("laravel_through_key"); // Select website_id and any other necessary fields
            }])
            ->withCount([
                'inventoryProducts as product_count' => function ($query) {
                    WebsiteProduct::whereIn('inventory_id', Inventory::where('department_id', "inventory_department.department_id")
                        ->where('status', 1)
                        ->pluck('id'));
                }
            ])
            ->orderBy('department_id', 'DESC')->get(); //inventory_department::getdepartment('');


        // return $depart;
        $sdepart = inventory_department::get_subdepart('');
        $sections = Section::getSection();
        $websites = DB::table("website_details")->where("company_id", session("company_id"))->where("status", 1)->get();

        return view('Invent_Department.lists', compact('depart', 'sdepart', 'websites', 'sections'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $depart = inventory_department::getdepartment('');
        // $sdepart = inventory_department::get_subdepart('');

        $sections = Section::getSection();
        $websites = DB::table("website_details")->where("company_id", session("company_id"))->where("status", 1)->get();
        return view('Invent_Department.create', compact('depart', 'websites', 'sections'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, inventory_department $invent_department, custom_helper $helper)
    {
        try {
            $imageName         = "";
            $bannerImageName   = "";
            $mobile_bannerName = "";

            // if (!empty($request->post('parent'))) {
            //     $exsist = $invent_department->subdepart_exists($request->deptname, $request->post('parent'));

            //     if ($exsist[0]->counter == 0) {
            //         if (!empty($request->file('departImage'))) {

            //              $rules = [
            //                         'departImage'=>'image|mimes:jpeg,png,jpg,webp|max:1024'
            //                       ];

            //              $validator = Validator::make($request->all(), $rules);

            //             // Check if validation fails
            //             if ($validator->fails()) {
            //                 // Redirect back with errors and old input
            //                 return response()->json(['error'=>$validator,'contrl'=>'departImage'],500);
            //             }

            //             $file = $this->uploads($request->file('departImage'), "images/department/");
            //             $imageName = !empty($file) ? $file["fileName"] : "";
            //         }

            //         $items = [
            //             'code'             => $request->code,
            //             'department_id'    => $request->post('parent'),
            //             'sub_depart_name'  => $request->deptname,
            //             'slug'             => preg_replace("/[\s_]/", "-", strtolower($request->deptname)),
            //             'image'            => $imageName,
            //         ];

            //         $result = $invent_department->insert_sdept($items);


            //         $getsubdepart = $invent_department->get_subdepartments($request->post('parent'));
            //         return 1;
            //     } else {
            //         return 0;
            //     }
            // } else {

            // return $invent_department->check_dept($request->get('deptname'),$request->get('code'));
            $messages = [];
            $rules = [
                'department_name' => 'required',
            ];

            if (!empty($request->get('department_code'))) { //checking department code exists
                if ($invent_department->check_depart_code($request->get('department_code'))) {

                    $rules['department_code'] = 'required';
                    $messages['department_code.required'] = 'This department code already exists.';
                }
            }

            if ($invent_department->check_dept($request->get('department_name'))) { //checking department name eixts
                $rules['department_name'] = 'required';
                $messages['department_name.required'] = 'This department name already exists.';
                $this->validate($request, $rules, $messages);
            }
            // else {

            if (!empty($request->file('department_image'))) { //department image
                $rules['department_image'] = 'image|mimes:jpeg,png,jpg,webp|max:1024';
                $file = $this->uploads($request->file('department_image'), "images/department/");
                $imageName = !empty($file) ? $file["fileName"] : "";
            }

            if (!empty($request->file('banner_image'))) { //department banner
                $rules['banner_image'] = 'image|mimes:jpeg,png,jpg,webp|max:1024';
                $file = $this->uploads($request->file('banner_image'), "images/department/");
                $bannerImageName = !empty($file) ? $file["fileName"] : "";
            }

            if (!empty($request->file('mobile_banner'))) { //department banner
                $rules['mobile_banner'] = 'image|mimes:jpeg,png,jpg,webp|max:1024';
                $file = $this->uploads($request->file('mobile_banner'), "images/department/");
                $mobile_bannerName = !empty($file) ? $file["fileName"] : "";
            }

            $this->validate($request, $rules); // validation module


            //department form details save array value
            $data = [
                'company_id'               => session('company_id'),
                'code'                     => $request->get('department_code'),
                'department_name'          => $request->get('department_name'),
                'website_department_name'  => (empty($request->website_department_name) ?  $request->get('department_iname') : $request->website_department_name),
                'date'                     => date('Y-m-d'),
                'time'                     => date('H:i:s'),
                'slug'                     => preg_replace("/[\s_]/", "-", strtolower($request->get('department_iname'))),
                "image"                    => $imageName,
                "banner"                   => $bannerImageName,
                "mobile_banner"            => $mobile_bannerName,
                "meta_title"               => $request->metatitle,
                "meta_description"         => $request->metadescript,
                'website_mode'             => isset($request->showWebsite) ? 1 : 0
            ];

            // department save to database
            $result = $invent_department->insert_dept($data);
            if ($result) {  //checking condition department issaved to database show the success message
                if (!empty($request->sections)) { //department section module
                    foreach ($request->sections as $value) {
                        $invent_department->insert_section(['department_id' => $result, 'section_id' => $value, 'created_at' => date('Y-m-d H:i:s')]);
                    }
                }

                Session::flash('success', 'Success!');
                //  $subdpt_value = $request->subdpt;

                //  $subdpt_value = explode(",",$subdpt_value);

                //  $code = $request->get('code');


                // for($i=0;$i < count($subdpt_value);$i++){

                //      $invent_department->insert_sdept([
                // 		'code'=>++$code,
                // 		'department_id'=>$result,
                // 		'sub_depart_name'=>$subdpt_value[$i],
                // 		'slug'=> preg_replace("/[\s_]/", "-",strtolower($subdpt_value[$i])),
                // 	]);

                // }
                // $msg = "ID # ".$result.", Name : ".$request->get('deptname');
                // $helper->sendPushNotification("New Department Added",$msg);
                // return response()->json(array("state" => 0, "msg" => '', "contrl" => ''));

            } else { //checking condition department is not to save database error condition true
                Session::flash('error', 'An error occurred while saving the data.');
                // return response()->json(array("state" => 1, "msg" => 'Not saved :(', "contrl" => ''));
            }

            return redirect()->route('invent_dept.create');
            // }
            // }
        } catch (Exception $e) {
            Log::error('Error saving data: ' . $e->getMessage());
            Session::flash('error', 'An error occurred while saving the data.');
            return redirect()->route('invent_dept.create');
        }
    }
    public function depart_update(Request $request, inventory_department $invent_department, custom_helper $helper)
    {
        if ($invent_department->check_edit_depart_name($request->get('id'), $request->get('depart'))) {
            return response()->json(array("state" => 1, "msg" => 'This department already exists.', "contrl" => 'udeptname'));
        } else {

            if ($invent_department->modify("inventory_department", ['department_name' => $request->get('depart'), 'slug' => preg_replace("/[\s_]/", "-", strtolower(get('depart')))], ['department_id' => $request->get('id')])) {

                //   return response()->json(['department_name'=>$request->get('depart'),'slug'=> preg_replace("/[\s_]/", "-",strtolower($request->get('depart')))]);
                $msg = "ID # " . $request->get('id') . ", Name : " . $request->get('depart');
                $helper->sendPushNotification("Department Updated", $msg);
                return response()->json(array('state' => 0, 'msg' => 'Saved changes :) '));
            } else {
                return response()->json(array('state' => 1, 'msg' => 'Oops! not saved changes :('));
            }
        }
    }
    public function sb_depart_update(Request $request, inventory_department $invent_department)
    {

        $imageName       = null;
        $bannerImageName = null;
        $mobile_banner   = null;

        if ($invent_department->check_edit_sub_depart_code($request->id, $request->code, $request->dept)) {
            return response()->json(array("state" => 1, "msg" => 'This Sub-department code already exists.', "contrl" => 'deptname'));
        }

        if ($invent_department->check_edit_sub_depart_name($request->id, $request->sdepart, $request->dept)) {
            return response()->json(array("state" => 1, "msg" => 'This Sub-Department name already exists.', "contrl" => 'deptname'));
        }
        // if($invent_department->check_sdept($request->get('id'),$request->get('sdepart'),$request->get('dept'),$request->get('code'))){
        // return response()->json(array("state"=>1,"msg"=>'This sub-department already exists.',"contrl"=>'tbx_'.$request->get('sdepart')));
        // }else {

        if (!empty($request->file('subdepartImage'))) {
            $file = $this->uploads($request->file('subdepartImage'), "images/department/");
            $imageName = !empty($file) ? $file["fileName"] : "";

            $get = DB::table('inventory_sub_department')->where('sub_department_id', $request->id)->first();
            if ($get) {
                $this->removeImage("images/department/", $get->image);
            }
        }

        $column = ['sub_depart_name' => $request->sdepart, 'website_sub_department_name' => (!empty($request->website_department_name) ? $request->website_department_name : $request->sdepart), 'slug' => preg_replace("/[\s_]/", "-", strtolower($request->sdepart))];
        if (!empty($request->file('subdepartBannerImage'))) {
            $file = $this->uploads($request->file('subdepartBannerImage'), "images/department/");
            $bannerImageName = !empty($file) ? $file["fileName"] : "";

            $get = DB::table('inventory_sub_department')->where('sub_department_id', $request->id)->first();
            if ($get != null) {
                $this->removeImage("images/department/", $get->banner);
            }
        }

        if (!empty($request->file('subdepartMobileBanner'))) {
            $file = $this->uploads($request->file('subdepartMobileBanner'), "images/department/");
            $mobile_banner = !empty($file) ? $file["fileName"] : "";

            $get = DB::table('inventory_sub_department')->where('sub_department_id', $request->id)->first();
            if ($get != null) {
                $this->removeImage("images/department/", $get->mobile_banner);
            }
        }

        $column = [
                    'sub_depart_name' => $request->sdepart,
                    'website_sub_department_name' =>(!empty($request->website_department_name) ? $request->website_department_name : $request->sdepart),
                     'slug' => preg_replace("/[\s_]/", "-", strtolower($request->sdepart))
                    ];

        if ($imageName != null) {
            $column['image'] = $imageName;
        }

        if ($bannerImageName != null) {
            $column['banner'] = $bannerImageName;
        }

        if ($mobile_banner != null) {
            $column['mobile_banner'] = $mobile_banner;
        }

        if ($request->get('code') != null) {
            $column['code'] = $request->get('code');
        }

        if ($invent_department->modify("inventory_sub_department", $column, ['sub_department_id' => $request->get('id')])) {
            return response()->json(array('state' => 0, 'msg' => 'Saved changes :) '));
        } else {
            return response()->json(array('state' => 1, 'msg' => 'Oops! not saved changes :('));
        }
        // }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(inventory_department $invent_department)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(inventory_department $invent_department, $id)
    {
        $depart = $invent_department->get_edit($id);

        if ($depart->count() > 0) {
            return response()->json($depart);
        } else {
            return response()->json(0);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, inventory_department $invent_department)
    {
        /*$result = $invent_department->modify(['department_name'=>$request->deptname],$request->hidd_id);

                if($invent_department->check_sdept($request->hidd_id)){
                   $invent_department->remove_sbdept($request->hidd_id);
                 }
                 $subdpt_value = explode(",",$request->subdpt);
                for($i=0;$i<count($subdpt_value);$i++){

                    $result = $invent_department->insert_sdept(['department_id'=>$request->hidd_id,'sub_depart_name'=>$subdpt_value[$i]]);
                }*/

        $result = false;
        if ($result) {
            return response()->json(array('state' => 1, 'msg' => 'Saved changes :) '));
        } else {
            return response()->json(array('state' => 0, 'msg' => 'Oops! not saved changes :(' . $request->subdpt));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(inventory_department $invent_department)
    {
        //
    }

    public function deletedepartment(Request $request)
    {
        try {
            if ($request->id != "") {
                DB::beginTransaction();
                DB::table("inventory_general")->where("department_id", $request->id)->update(["status" => 2]);
                DB::table("inventory_sub_department")->where("department_id", $request->id)->update(["status" => 2]);
                DB::table("inventory_department")->where("department_id", $request->id)->update(["status" => 2]);
                DB::table('website_products')
                    ->join('inventory_general', 'website_products.inventory_id', '=', 'inventory_general.id')
                    ->where('inventory_general.department_id', $request->id)
                    ->where('inventory_general.status', 2)
                    ->update(['website_products.status' => 0, 'website_products.updated_at' => date("Y-m-d H:i:s")]);
                DB::table('inventory_department_sections')->where('department_id',$request->id)->delete();
                DB::commit();
                return response()->json(["status" => 200, "message" => "Department Deleted successfully."]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["status" => 500, "message" => "Error: " . $e->getMessage()]);
        }
    }

    public function remove_sbdept(Request $request){
        try {
            if ($request->id != "") {
                DB::table("inventory_sub_department")->where("sub_department_id", $request->id)->update(["status" => 2]);
                return response()->json("Department Deleted successfully.",200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(),500);
        }

    }

    public function adddepartment(inventory_department $in_depart, Request $request)
    {
        $exsist = $in_depart->depart_exists($request->departname);
        if ($exsist[0]->counter == 0) {
            $items = [
                'company_id' => session('company_id'),
                'department_name'         => $request->departname,
                'website_department_name' => $request->departname,
                'slug' => preg_replace("/[\s_]/", "-", strtolower($request->departname)),
                'date' => date('Y-m-d'),
                'time' => date('H:i:s')
            ];
            $result = $in_depart->insert_dept($items);
            $getdepart = $in_depart->get_departments();
            return $getdepart;
        } else {
            return 0;
        }
    }
    public function addsubdepartment(inventory_department $in_depart, Request $request)
    {
        $imageName = null;
        $bannerImageName = null;
        $mobile_banner   = null;

        $exsist = $in_depart->subdepart_exists($request->subdepart, $request->departid);
        if ($exsist[0]->counter == 0) {

            if (!empty($request->file('subdepartImage'))) {
                $file = $this->uploads($request->file('subdepartImage'), "images/department/");
                $imageName = !empty($file) ? $file["fileName"] : "";
            }

            if (!empty($request->file('subdepartBanner'))) {
                $file = $this->uploads($request->file('subdepartBanner'), "images/department/");
                $bannerImageName = !empty($file) ? $file["fileName"] : "";
            }

            if (!empty($request->file('mobile_banner_sbdepart'))) {
                $file = $this->uploads($request->file('mobile_banner_sbdepart'), "images/department/");
                $mobile_banner = !empty($file) ? $file["fileName"] : "";
            }

            $items = [
                'code'                         => $request->code,
                'department_id'                => $request->departid,
                'sub_depart_name'              => $request->subdepart,
                'website_sub_department_name'  => empty($request->websubdepart) ? $request->subdepart : $request->websubdepart,
                'slug'                         => preg_replace("/[\s_]/", "-", strtolower($request->subdepart)),
                'image'                        => $imageName,
                'banner'                       => $bannerImageName,
                'mobile_banner'                => $mobile_banner,
                'website_mode'                 => isset($request->showWebsite) ? 1 : 0,
            ];
            $result = $in_depart->insert_sdept($items);
            $getsubdepart = $in_depart->get_subdepartments($request->departid);
            return $getsubdepart;
        } else {
            return 0;
        }
    }
    public function updatedepart(inventory_department $in_depart, Request $request)
    {
        $imageName = null;
        $bannerImageName = null;
        $mobile_banner = null;

        if (!empty($request->editcode)) {
            if ($in_depart->check_edit_depart_code($request->departid, $request->editcode)) {
                return response()->json(array("state" => 1, "msg" => 'This department code already exists.', "contrl" => 'codeid'));
            }
        }

        if ($in_depart->check_edit_depart_name($request->departid, $request->departname)) {
            return response()->json(array("state" => 1, "msg" => 'This department name already exists.', "contrl" => 'deptname'));
        }

        if (!empty($request->file('departImage'))) {

            $get = DB::table('inventory_department')->where('company_id', session('company_id'))->where('department_id', $request->departid)->first();

            $file = $this->uploads($request->file('departImage'), "images/department/", ($get != null ? $get->image : ''));
            $imageName = !empty($file) ? $file["fileName"] : "";
        }

        if (!empty($request->file('bannerImage'))) {
            //  $rules = [
            //             'bannerImage'=>'image|mimes:jpeg,png,jpg,webp|max:1024'
            //           ];

            //  $validator = Validator::make($request->all(), $rules);

            // // Check if validation fails
            // if ($validator->fails()) {
            //     // Redirect back with errors and old input
            //     return response()->json(['error'=>$validator,'contrl'=>'departImage'],500);
            // }

            $get = DB::table('inventory_department')->where('company_id', session('company_id'))->where('department_id', $request->departid)->first();

            $file = $this->uploads($request->file('bannerImage'), "images/department/");
            $bannerImageName = !empty($file) ? $file["fileName"] : "";

            if ($get != null) {
                $this->removeImage("images/department/", $get->banner);
            }
        }

        if (!empty($request->file('mobile_banner'))) {

            $get = DB::table('inventory_department')->where('company_id', session('company_id'))->where('department_id', $request->departid)->first();

            $file = $this->uploads($request->file('mobile_banner'), "images/department/");
            $mobile_banner = !empty($file) ? $file["fileName"] : "";

            if ($get != null) {
                $this->removeImage("images/department/", $get->mobile_banner);
            }
        }


        $items = [
            'code'                     => $request->editcode,
            'department_name'          => $request->departname,
            'website_department_name'  => (empty($request->webdeptname) ?  $request->departname : $request->webdeptname),
            'slug'                     => preg_replace("/[\s_]/", "-", strtolower($request->departname)),
            'website_mode'             => (isset($request->showWebsite) ? 1 : 0),
            'date'                     => date('Y-m-d'),
            'time'                     => date('H:i:s'),
        ];

        if ($imageName != null) {
            $items['image'] = $imageName;
        }

        if ($bannerImageName != null) {
            $items['banner'] = $bannerImageName;
        }

        if ($mobile_banner != null) {
            $items['mobile_banner'] = $mobile_banner;
        }

        if (isset($request->showWebsite)) {
            $items['meta_title']       = $request->metatitle;
            $items['meta_description'] = $request->metadescript;
        }

        $result = $in_depart->update_depart($request->departid, $items);

        if (!empty($request->sections)) {
            $in_depart->remove_section($request->departid);
            foreach ($request->sections as $value) {
                $in_depart->insert_section(['department_id' => $request->departid, 'section_id' => $value, 'created_at' => date('Y-m-d H:i:s')]);
            }
        }

        return response()->json(array("state" => 0, "msg" => 'Department edit successfully.', "contrl" => 'deptname'));;
    }
    public function getsubdepart(inventory_department $in_depart, Request $request)
    {
        $getsubdepart = $in_depart->get_subdepartments($request->departid);
        return $getsubdepart;
    }



    // public function department_website_connect(Request $request)
    // {

    //     $statusCode = $request->status_code;
    //     $department = $request->department;
    //     $website_id = $request->website_id;

    //     if (empty($department)  && empty($statusCode)) { //&& ( $statusCode == 'link' && empty($website_id))
    //         // return $request;
    //         return response()->json('Server Issue! parameter not found!', 500);
    //     }

    //     if ($department != ""  && $statusCode != "") {

    //         if ($statusCode == 'link') {
    //             $inventories = Inventory::where('department_id', $department)->where('status', 1)->pluck('id');
    //             if ($inventories != null) {
    //                 foreach ($inventories as $value) {
    //                     if (
    //                         WebsiteProduct::where('website_id', $website_id)
    //                         ->where('inventory_id', $value->id)
    //                         ->where('status', 1)->count() == 0
    //                     ) {
    //                         WebsiteProduct::create([
    //                             'website_id'   => $website_id,
    //                             'inventory_id' => $value->id,
    //                             'created_at'   => date("Y-m-d H:i:s")
    //                         ]);
    //                     }
    //                 }
    //                 return response()->json('Success!', 200);
    //             }
    //         }

    //         if ($statusCode == 'unlink') {
    //             $inventories = WebsiteProduct::whereIn(
    //                 'inventory_id',
    //                 Inventory::where('department_id', $department)
    //                     ->where('status', 1)->pluck('id')
    //             )
    //                 ->where('status', 1)
    //                 ->update(['status' => 0, 'updated_at' => date("Y-m-d H:i:s")]);

    //             if ($inventories) {
    //                 return response()->json('Success!', 200);
    //             }
    //         }
    //     }else{
    //         return response()->json('Server Issue! parameter not found!', 500);
    //     }
    // }

    public function departmentWebsiteConnect(Request $request)
    {
        $statusCode = $request->input('status_code');
        $department = $request->input('department');
        $websiteId = $request->input('website_id');

        // Validate input
        if (empty($department) || empty($statusCode)) {
            return response()->json('Server Issue! Parameter not found!', 500);
        }

        // Get active inventories related to the department
        $inventoryIds = Inventory::where('department_id', $department)
            ->where("company_id",session("company_id"))
            ->where('status', 1)
            ->pluck('id');

        if ($inventoryIds->isEmpty()) {
            return response()->json('No active inventories found for this department.', 404);
        }

        if ($statusCode === 'link') {
            // Find inventories that are not already linked to the website
            $existingWebsiteProducts = WebsiteProduct::where('website_id', $websiteId)
                ->whereIn('inventory_id', $inventoryIds)
                ->where('status', 1)
                ->pluck('inventory_id')
                ->toArray();

            // Filter out already linked inventories
            $newInventoryIds = $inventoryIds->diff($existingWebsiteProducts);

            // Insert new WebsiteProduct records for inventories that aren't linked yet
            $newWebsiteProducts = $newInventoryIds->map(function ($inventoryId) use ($websiteId) {
                return [
                    'website_id' => $websiteId,
                    'inventory_id' => $inventoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'status' => 1
                ];
            });

            if ($newWebsiteProducts->isNotEmpty()) {
                WebsiteProduct::insert($newWebsiteProducts->toArray());
            }

            return response()->json('Success!', 200);
        }

        if ($statusCode === 'unlink') {
            // Unlink inventories from the website by marking them as inactive
            $updated = WebsiteProduct::whereIn('inventory_id', $inventoryIds)
                ->where('status', 1)
                ->update(['status' => 0, 'updated_at' => now()]);

            if ($updated) {
                return response()->json('Success!', 200);
            }

            return response()->json('No inventories were updated.', 404);
        }

        return response()->json('Invalid status code!', 400);
    }
}
