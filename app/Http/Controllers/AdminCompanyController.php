<?php

namespace App\Http\Controllers;

use App\adminCompany;
use App\branch;
use App\Traits\MediaTrait;
use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\DB;
use \stdClass;

class AdminCompanyController extends Controller
{
    use MediaTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {


        $company = adminCompany::get_company();
        return view('Admin.Company.list', compact('company'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $country = adminCompany::getcountry();
        $city = adminCompany::getcity();
        $currencies = DB::table('currencies')->get();
        $packages = DB::table('packages')->get();
        return view('Admin.Company.create', compact('country', 'city', 'currencies', 'packages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, adminCompany $adminCompany, branch $branch)
    {
        // $imageName = "";
        // $posbg = "";
        // $orderCallingBg = "";

        $rules = [
            'companyname' => 'required',
            'country' => 'required',
            'city' => 'required',
            'company_email' => 'required',
            'company_mobile' => 'required',
            'company_ptcl' => 'required',
            'company_address' => 'required',
            'vdimg' => 'required',
        ];
        $this->validate($request, $rules);


        if (!empty($request->vdimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            // $imageName = time().'.'.$request->vdimg->getClientOriginalExtension();
            // $img = Image::make($request->vdimg)->resize(200, 200);
            // $res = $img->save(public_path('assets/images/company/'.$imageName), 75);
            // $res = $img->save(public_path('assets/images/branch/'.$imageName), 75);
            $file = $this->uploads($request->vdimg, "images/company/");
            //              $imageName = time().'.'.$request->vdimg->getClientOriginalExtension();
            //              $imageName = trim($imageName," "); //Removes white spaces from the string
            //              $request->vdimg->move(public_path('assets/images/company/'), $imageName);
        }

        if (!empty($request->posbgimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            // $posbg = time().'.'.$request->posbgimg->getClientOriginalExtension();
            // $img = Image::make($request->posbgimg)->resize(800, 600);
            // $res = $img->save(public_path('assets/images/pos-background/'.$posbg), 100);

            $posbg = $this->uploads($request->posbgimg, "images/pos-background/");

            //            $posbg = time().'.'.$request->posbgimg->getClientOriginalExtension();
            //            $posbg = trim($posbg," "); //Removes white spaces from the string
            //            $request->posbgimg->move(public_path('assets/images/pos-background/'), $posbg);
        }

        if (!empty($request->ordercallingbgimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);

            $orderbg = $this->uploads($request->ordercallingbgimg, "images/order-calling/");
        }


        $items = [
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->companyname,
            'address' => $request->company_address,
            'email' => $request->company_email,
            'ptcl_contact' => $request->company_ptcl,
            'mobile_contact' => $request->company_mobile,
            'latitude' => null,
            'longitude' => null,
            'logo' => $file["fileName"] ?? "",
            'pos_background' => $posbg["fileName"] ?? "",
            'order_calling_display_image' => $orderbg["fileName"] ?? "",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'package_id' => $request->package,
        ];

        $result =  $adminCompany->insert($items);

        if ($request->currency != "") {
            $myObj = new stdClass();
            $myObj->currency = $request->currency;
            $myJSON = json_encode($myObj);

            DB::table("settings")->insert([
                "company_id" =>  $result,
                "data" =>  $myJSON,
            ]);
        }

        $items =
            [
                'company_id' =>  $result,
                'country_id' => $request->country,
                'city_id' => $request->city,
                'status_id' => 1,
                'branch_name' => "Head Office -" . $request->companyname,
                'branch_address' => $request->company_address,
                'branch_latitude' => null,
                'branch_longitude' => null,
                'branch_ptcl' => $request->company_ptcl,
                'branch_mobile' => $request->company_mobile,
                'branch_email' => $request->company_email,
                'branch_logo' => $file["fileName"] ?? "",
                'modify_by' => session('userid'),
                'modify_date' => date('Y-m-d'),
                'modify_time' => date('H:i:s'),
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            ];

        $branch = $branch->insert_branch($items);

        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function show(adminCompany $adminCompany) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, adminCompany $adminCompany)
    {
        $country = adminCompany::getcountry();
        $city = adminCompany::getcity();
        $company = $adminCompany->getCompanyById($request->id);
        $currencies = DB::table('currencies')->get();
        $setting = DB::table('settings')->where("company_id", $request->id)->get();
        $setting = json_decode($setting[0]->data, true);
        $currencyname = $setting["currency"];
        $packages = DB::table('packages')->get();
        return view('Admin.Company.edit', compact('country', 'city', 'company', 'currencies', 'currencyname', 'packages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, adminCompany $adminCompany)
    {
        $imageName = "";
        $posbg = "";

        if (!empty($request->posbgimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $bgFile = $this->uploads($request->posbgimg, "images/pos-background/", $request->pos_bg_logo);
        }

        if (!empty($request->vdimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $file = $this->uploads($request->vdimg, "images/company/", $request->prev_logo);
        }
        
        if (!empty($request->ordercallingbgimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $orderFile = $this->uploads($request->ordercallingbgimg, "images/order-calling/", $request->prev_order_calling_display);
        }

        $items = [
            'status_id' => 1,
            'country_id' => $request->country,
            'city_id' => $request->city,
            'name' => $request->companyname,
            'address' => $request->company_address,
            'email' => $request->company_email,
            'ptcl_contact' => $request->company_ptcl,
            'mobile_contact' => $request->company_mobile,
            'logo' => (!empty($request->vdimg) ? $file["fileName"] : $request->prev_logo ),
            'pos_background' => (!empty($request->posbgimg) ? $bgFile["fileName"] : $request->pos_bg_logo),
            'order_calling_display_image' => (!empty($request->ordercallingbgimg) ? $orderFile["fileName"] : $request->prev_order_calling_display),
            'updated_at' => date('Y-m-d H:i:s'),
            'package_id' => $request->package,
        ];

        $result =  $adminCompany->updateCompany($items, $request->company_id);

        if ($request->currency != "") {
            $myObj = new stdClass();
            $myObj->currency = $request->currency;
            $myJSON = json_encode($myObj);

            DB::table("settings")->where("company_id", $request->company_id)->update([
                "data" =>  $myJSON,
            ]);
        }

        return redirect()->route('company.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\adminCompany  $adminCompany
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, adminCompany $adminCompany)
    {
        $details = $adminCompany->getCompanyById($request->id);
        $result =  $adminCompany->deleteCompany($request->id);
        if ($result) {
            $this->removeImage("images/company/", $details[0]->logo);
        }
        // $image_path = public_path('assets/images/company/' . $details[0]->logo);  // Value is not URL but directory file path
        // if ($details[0]->logo != "") {
        //     unlink($image_path);
        // }
        return $result;
    }
}
