<?php

namespace App\Http\Controllers;

use App\delivery;
use App\userDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Vendor;
use App\ServiceProvider;
use App\Traits\MediaTrait;
use Image;
use \dPDF;


class DeliveryController extends Controller
{
    use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(delivery $delivery){
        $getbranch = $delivery->getbranches();
        $charges = $delivery->getcharges(1);
        return view('Delivery.delivery-charges', compact('getbranch','charges'));
    }

	public function mobilePromotion(){
		$images = DB::table("mobile_promotion_images")->join("inventory_general","inventory_general.id","=","mobile_promotion_images.product_id")->where("mobile_promotion_images.company_id",session("company_id"))->select("mobile_promotion_images.*","inventory_general.product_name")->get();
		$products = DB::table("inventory_general")->where("status",1)->where("company_id",session("company_id"))->select(["id","product_name"])->get();
        return view('Promotion.mobile-promotion-images',compact('images','products'));
    }

	public function insertMobilePromotion(Request $request){
		$dbcount =  DB::table("mobile_promotion_images")->where("company_id",session("company_id"))->count();
		$count=  count($request->image);
		if($dbcount != 5){
			if ($request->image != "") {
				 foreach ($request->file('image') as $key => $image) {
					$imageName = time().'-'.session("company_id").'-'.($key+1).'.'. $image->getClientOriginalExtension();
					$img = Image::make($image)->resize(486, 216);
					$res = $img->save(public_path('assets/images/mobile/' . $imageName), 75);
					$data[] = $imageName;
				}

				//Inventory Images Here
				foreach ($data  as $value) {
					DB::table("mobile_promotion_images")->insert([
						"company_id" => session("company_id"),
						"product_id" => $request->product,
						"description" => $request->description,
						"image" => $value
					]);
				}

				return redirect('mobile-promotion')->With('status', 'Images Uploaded successfully');
			}
		}else{
			return redirect('mobile-promotion')->With('status', 'You have already five images uploaded');
		}
	}

	public function mobilePromoImageDelete(Request $request)
	{
		if (DB::table("mobile_promotion_images")->where('id', $request->id)->delete()) {
            $image_path = public_path('assets/images/mobile/' . $request->image);  // Value is not URL but directory file path
            if ($request->image != "") {
                unlink($image_path);
               return response()->json(["message" => "Image Deleted Successfully","status" => 200]);
            } else {
                 return response()->json(["message" => "Some error found","status" => 500]);
            }
        } else {
             return response()->json(["message" => "Record not found","status" => 404]);
        }
	}

    public function store(delivery $delivery, Request $request){

        $chk = $delivery->exsist_chk($request->areaname, $request->branch);
        if ($chk[0]->counts == 0) {
            $items=[
                'area_name' => $request->areaname,
                'charges' => $request->charges,
                'status_id' =>1,
                'branch_id' =>$request->branch,
            ];
            $charges = $delivery->insert('delivery_charges',$items);
            return  1;
        }
        else{
            return 0;
        }

    }

    public function serviceProviderLedgerPDF(delivery $delivery, Request $request,Vendor $vendor){
        $data = array();
        $company = $vendor->company(session('company_id'));
        $data = $delivery->getServiceProviderInfo(Crypt::decrypt($request->provide_id),$request->from,$request->to);
        // dd($data);
        $from = $request->from;
        $to = $request->to;
        $provider_id = Crypt::decrypt($request->provide_id);
        // $returnHTML = view('Delivery.ledgerPdf')->with('data', $data)->render();
        // echo $returnHTML;exit;
        $pdf = dPDF::loadView('Delivery.ledgerPdf',compact('company','data','from','to','provider_id'));
        // Closed to ledger
        if($request->closed == 'true'){
            $data = $delivery->closedToServiceProviderLedger(Crypt::decrypt($request->provide_id),$request->from,$request->to);
        }
        return  $pdf->stream("ledger-report.pdf", array("Attachment" => 0));
         exit;
    }

    public function update(delivery $delivery, Request $request){

        $items=[
            'area_name' => $request->areaname,
            'charges' => $request->charges,
            'status_id' =>1,
            'branch_id' =>$request->branch,
        ];
        $charges = $delivery->update_charges($request->chargesid,$items);
        return  1;
    }

    public function inactivecharges(delivery $delivery, Request $request){

        $items=[
            'status_id' =>2,
        ];
        $charges = $delivery->update_charges($request->chargesid,$items);
        return  1;
    }

    public function inactive(delivery $delivery, Request $request){
        $charges = $delivery->getcharges(2);
        return  $charges;
    }

    public function reactive(delivery $delivery, Request $request){

        $items=[
            'status_id' =>1,
        ];
        $charges = $delivery->update_charges($request->chargesid,$items);
        return  1;
    }

    public function store_category(delivery $delivery, Request $request){

        $chk = $delivery->exsist_chk_category($request->category);
        if ($chk[0]->counts == 0) {
            $items=[
                'category' => $request->category,
            ];
            $charges = $delivery->insert('service_provider_category',$items);

            $getcategory = $delivery->getcategory();
            return $getcategory;
        }
        else{
            return 0;
        }

    }

	public function checkServiceProviderName(delivery $delivery, Request $request)
	{
        $branch = "";
        if (session("roleId") == 2) {
            $branch = $request->branch;
        }else{
            $branch = session('branch');
        }
		$chk = $delivery->exsist_chk_provider($request->providername,$branch);
		return $chk;
	}

    public function storeserviceprovider(delivery $delivery, Request $request,userDetails $users){
		$rules = [
            'providername' => 'required',
            'branch' => 'required',
            'category' => 'required',
            'person' => 'required',
			// 'percentage' => 'required',
			'paymenttype' => 'required',
			'paymentValue' => 'required',
            'address' => 'required',
            'contact' => 'required',
			'username' => 'required',
			'password' => 'required',
        ];

		if($request->category == 2 ){
			// $rules = array_merge($rules,[
				// "cnic" => 'required',
			// ]);
		}


        $this->validate($request, $rules);

        $imageName= "";
        $chk = $delivery->exsist_chk_provider($request->providername,$request->branch);
        if ($chk[0]->counts == 0) {
            if(!empty($request->image)){
                // $imageName = time().".".$request->image->getClientOriginalExtension();
                // $img = Image::make($request->image)->resize(250, 250);
                // $img->save(public_path('assets/images/service-provider/'.$imageName), 75);
                $result = $this->uploads($request->image, "images/service-provider/", "", []);
                $imageName = $result["fileName"];
            }
            $items=[
                'provider_name' => $request->providername,
                'contact' => $request->contact,
                'cnic_ntn' => $request->cnic,
                'address' => $request->address,
                'categor_id' => $request->category,
                'branch_id' => $request->branch,
                'status_id' => 1,
                'person' => $request->person,
                // 'percentage_id' => $request->percentage,
				'payment_type_id' => $request->paymenttype,
				'payment_value' => $request->paymentValue,
                'image' => $imageName,
            ];
            $provider = $delivery->insert('service_provider_details',$items);
            /* Service Provide bulk insertion */
            $arrData =array();
            $chargeName = $request->get('chargeName');
            $chargeValue = $request->get('chargeValue');
            $type = $request->get('type');
            if($chargeName){
                foreach($chargeName as $key => $n ) {
                    $arrData[] = array("type"=>$type[$key],"provider_id"=>$provider,"chargeName"=>$chargeName[$key], "chargeValue"=>$chargeValue
                        [$key]);
                }
                DB::table('service_provide_additional_charges')->insert($arrData);
            }

            if ($request->prebal < 0) {
                $items=[
                    'provider_id' => $provider,
                    'debit' => 0,
                    'credit' => $request->prebal,
                    'balance' => $request->prebal,
                    'order_id' => 0,
                    'narration' => 'Previous balance',
                ];
                $ledger = $delivery->insert('service_provider_ladger',$items);
            }
            else{
                $items=[
                    'provider_id' => $provider,
                    'debit' => $request->prebal,
                    'credit' => 0,
                    'balance' => $request->prebal,
                    'order_id' => 0,
                    'narration' => 'Previous balance',
                ];
                $ledger = $delivery->insert('service_provider_ladger',$items);
            }
            $password = $request->password;
            $items=[
                'fullname' => $request->person,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => '',
                'contact' => $request->contact,
                'country_id' => 179,
                'city_id' => 1,
                'address' => $request->address,
                'image' => "",
                'remember_token' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'show_password' => $password,
            ];

            $user = $users->insert_user('user_details',$items);

            $items = [
                'user_id' => $user,
                'company_id' => session('company_id'),
                'branch_id' => $request->branch,
                'role_id' => 5,
                'status_id' => 1,
            ];
            $result = $users->insert_user('user_authorization',$items);

            $relation = [
                'user_id' => $user,
                'provider_id' => $provider,
            ];
            $relation = $delivery->insert('user_salesprovider_relation',$relation);


            return redirect("service-provider");
        }
        else{
            return 0;
        }

    }

    public function updateAdditionalCharge(Request $request,delivery $delivery){
         $rules = [
            'chargeName' => 'required',
            'chargeValue' => 'required',
        ];
        $validator = Validator::make(request()->all(), $rules);
        if ($request->isMethod('post') && $request->ajax()) {
            if ($validator->fails()) {
                return response(['status' => "false", 'message' => 'Please enter to charge name or charge value']);
            }

            $fields = [
                'chargeName' => $request->chargeName,
                'chargeValue' => $request->chargeValue,
                'type' => $request->type,
            ];
            DB::table('service_provide_additional_charges')->where('id',$request->addition_charges_id)->update($fields);
            return response(['status' => "true", 'message' => 'Additional charge updated.']);
        }
    }

    public function updateDeliveryNarration(Request $request){
         $rules = [
            'narration' => 'required',
        ];
        $validator = Validator::make(request()->all(), $rules);
        if ($request->isMethod('post') && $request->ajax()) {
            if ($validator->fails()) {
                return response(['status' => "false", 'message' => 'Please enter to narration']);
            }
            $fields = [
                'narration' => $request->narration,
            ];
            DB::table('service_provider_ladger')->where('ladger_id',$request->service_provider_narration_id)->update($fields);
            return response(['status' => "true", 'message' => 'Narration updated.']);
        }

    }


    public function show_create(delivery $delivery){
        $getbranch = $delivery->getbranches();
        $getcategory = $delivery->getcategory();
        $getpercen = $delivery->getpercentages();
		$providersPaymentType = $delivery->getServiceProviderPaymentInfo();
        $website = DB::table('website_details')
                      ->where('company_id',session('company_id'))
                      ->where('status',1)
                      ->get();
        return view('Delivery.service-provider', compact('getbranch','getcategory','getpercen','providersPaymentType','website'));
    }


    public function show(delivery $delivery){
        $providers = $delivery->getserviceproviders(1);

        return view('Delivery.service-provider-list', compact('providers'));
    }

    public function providerledger(delivery $delivery, request $request){
        $providerID = $request->id;
        $details = $delivery->getledger(Crypt::decrypt($request->id));

        return view('Delivery.serviceprovider-ledger', compact('details','providerID'));
    }


    public function store_ledger(delivery $delivery, Request $request){

        $prebal = $delivery->getpreviousbalance($request->providerid);

        if ($request->mode == 1) {
            //agar balance minus me ae ga to

            if ($prebal[0]->balance < 0) {
                $bal = $prebal[0]->balance + $request->amount;
            }
            else if ($prebal[0]->balance == 0) {
                return 0;
            }
            //agar balance positive me ae ga to
            else{
                $bal = $prebal[0]->balance - $request->amount;
            }

            $items=[
                'provider_id' =>$request->providerid,
                'debit' => $request->amount,
                'credit' => 0,
                'balance' => $bal,
                'order_id' => 0,
                'narration' => $request->narration,
            ];
            $ledger = $delivery->insert('service_provider_ladger',$items);

        }
        else{
            //agar balance minus me ae ga to

            if ($prebal[0]->balance <= 0) {
                $bal = $prebal[0]->balance - $request->amount;
            }
            //agar balance positive me ae ga to
            else{
                $bal = $prebal[0]->balance + $request->amount;
            }

            $items=[
                'provider_id' =>$request->providerid,
                'debit' => 0,
                'credit' => $request->amount,
                'balance' => $bal,
                'order_id' => 0,
                'narration' => $request->narration,
            ];
            $ledger = $delivery->insert('service_provider_ladger',$items);

        }
        return 1;

    }

    public function inactiveprovider(delivery $delivery, Request $request){

        $items=[
            'status_id' => 2,
        ];
        $charges = $delivery->update_provider($request->providerid,$items);
        return  1;
    }

    public function getinactiveprovider(delivery $delivery, Request $request){
        $providers = $delivery->getserviceproviders(2);
        return  $providers;
    }

    public function reactiveprovider(delivery $delivery, Request $request){

        $items=[
            'status_id' => 1,
        ];
        $charges = $delivery->update_provider($request->providerid,$items);
        return  1;
    }


    public function store_per(delivery $delivery, Request $request){

        $chk = $delivery->exsist_chk_percentage($request->percentage);
        if ($chk[0]->counts == 0) {
            $items=[
                'percentage' => $request->percentage,
            ];
            $charges = $delivery->insert('service_agreement_percentages',$items);

            $percentage = $delivery->getpercentages();
            return $percentage;
        }
        else{
            return 0;
        }

    }


    public function edit(delivery $delivery, request $request){
        $providerId = Crypt::decrypt($request->id);
        $getbranch = $delivery->getbranches();
        $getcategory = $delivery->getcategory();
        $getpercen = $delivery->getpercentages();
		$providersPaymentType = $delivery->getServiceProviderPaymentInfo();
        $details = $delivery->getdetails($providerId);
        $getAdditionalCharges = $delivery->getAdditionalCharges($providerId);
        $id = $request->id;
        return view('Delivery.service-provider-edit', compact('getbranch','getcategory','getpercen','details','id','getAdditionalCharges','providersPaymentType'));
    }



    public function updateserviceprovider(delivery $delivery, Request $request){
        $imageName = "";
        if(!empty($request->image)){
            if($request->prev_image != ""){
                $path = public_path('assets/images/service-provider/'.$request->prev_image);
                if(file_exists($path)){
                    unlink($path);
                }
            }
            $result = $this->uploads($request->image, "images/service-provider/", "", []);
            // $imageName = time().".".$request->image->getClientOriginalExtension();
            // $img = Image::make($request->image)->resize(250, 250);
            // $img->save(public_path('assets/images/service-provider/'.$imageName), 75);
        }
        $items=[
            'provider_name' => $request->providername,
            'contact' => $request->contact,
            'cnic_ntn' => $request->cnic,
            'address' => $request->address,
            'categor_id' => $request->category,
            'branch_id' => $request->branch,
            'status_id' => 1,
            'person' => $request->person,
            // 'percentage_id' => $request->percentage,
			'payment_type_id' => $request->paymenttype,
			'payment_value' => $request->paymentValue,
            'image' => (!empty($request->image) ? $result["fileName"] : $request->prev_image)
        ];

        $provider = $delivery->update_provider($request->proid,$items);

        $userId = $delivery->getUserIdFromServiceProvider($request->proid);
        if ($userId > 0) {
            $result = $delivery->update_service_provider_user($userId,$request->person,$request->branch);
        }

         /* Service Provide bulk insertion */
            $arrData =array();
            $chargeName = $request->get('chargeName');
            $chargeValue = $request->get('chargeValue');
            $type = $request->get('type');
            if($chargeName){
                foreach($chargeName as $key => $n ) {
                    $arrData[] = array("type"=>$type[$key],"provider_id"=>$request->proid,"chargeName"=>$chargeName[$key], "chargeValue"=>$chargeValue
                        [$key]);
                }
                DB::table('service_provide_additional_charges')->insert($arrData);
            }
        return redirect('service-provider');
    }





}
