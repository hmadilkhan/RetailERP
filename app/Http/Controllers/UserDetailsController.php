<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BranchService;
use App\Traits\MediaTrait;
use App\userDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Image;


class UserDetailsController extends Controller
{
    use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(userDetails $users)
    {
        $getusers = $users->get_users();
        // return $getusers;
        return view('Users.list', compact('getusers'));
    }

    public function getBranchesByCompany(Request $request)
    {
        if ($request->company != "") {
            return DB::table("branch")->where("company_id", $request->company)->where("status_id",1)->get();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(userDetails $users,BranchService $branchService)
    {
        $country = $users->getcountry();
        $city = $users->getcity();
        $role = $users->getroles();
        // $branch = $users->getbranches();
        $branch = $branchService->getBranches();
        $company = $users->getCompany();
        return view('Users.create', compact('country', 'city', 'role', 'branch', 'company'));
    }

    public function chk_user_exists(Request $request, userDetails $users)
    {
        $count = $users->chk_user($request->username);
        return $count;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, userDetails $users)
    {
        // $imageName = "";
        $file = [];
        $rules = [
            'branch' => 'required',
            'company' => 'required',
            'role' => 'required',
            'fullname' => 'required',
            'country' => 'required',
            'city' => 'required',
            // 'username' => 'required',
            // 'password' => 'required',
        ];
        $this->validate($request, $rules);

        $exist = $users->exist($request->username);
        if ($exist == 0) {
            if (!empty($request->vdimg)) {
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                ]);
                //   $imageName = $request->username.'.'.$request->vdimg->getClientOriginalExtension();
                //   $img = Image::make($request->vdimg)->resize(600, 600);
                //   $res = $img->save(public_path('assets/images/users/'.$imageName), 75);
                $file  = $this->uploads($request->vdimg, 'images/users/');
            }
            // This condition is for checking Regional Manager 
            $branch = ($request->role == 16 || $request->role == 18 ?  $request->branch[0] : $request->branch);

            $items = [
                'fullname' => $request->fullname,
                'username' => ($request->username == "" ? "qurban-" : $request->username),
                'password' => ($request->password == "" ? Hash::make("1234") : Hash::make($request->password)),
                'email' => $request->email,
                'contact' => $request->contact,
                'country_id' => $request->country,
                'city_id' => $request->city,
                'address' => $request->address,
                'image' => !empty($request->vdimg) ? $file["fileName"] :  "",
                'remember_token' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'show_password' => $request->password,
                'role_id' => $request->role,
                'company_id' => $request->company,
                'branch_id' => $branch,
            ];
            $user = $users->insert_user('user_details', $items);
            $items = [
                'user_id' => $user,
                'company_id' => $request->company,
                'branch_id' => $branch,
                'role_id' => $request->role,
                'status_id' => 1,
            ];
            $result = $users->insert_user('user_authorization', $items);

            if (in_array($request->role, [16, 18])) {
                $count = count($request->branch);
                for ($i = 0; $i < $count; $i++) {
                    $items = [
                        'user_id' => $user,
                        'branch_id' => $request->branch[$i],
                    ];
                    $result = $users->insert_user('user_branches', $items);
                }
            }
            return redirect('usersDetails');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\userDetails  $userDetails
     * @return \Illuminate\Http\Response
     */
    public function show(userDetails $userDetails)
    {
        //
    }


    public function edit(Request $request, userDetails $users)
    {
        $country = $users->getcountry();
        $city = $users->getcity();
        $role = $users->getroles();
        $branch = $users->getbranches();
        $company = $users->getCompany();

        $userdetails = $users->user_details(Crypt::decrypt($request->id));
        $branch = DB::table("branch")->where("company_id", (!empty($userdetails) ? $userdetails[0]->company_id : ''))->get();
        $userBranches = DB::table("user_branches")->where("user_id", $userdetails[0]->id)->pluck("branch_id");
        return view('Users.edit', compact('country', 'city', 'role', 'branch', 'userdetails', 'company', 'userBranches'));
    }

    public function update(Request $request, userDetails $users)
    {
        $rules = [
            'fullname' => 'required',
            'username' => 'required',
            'password' => 'required',
        ];
        $this->validate($request, $rules);
        // This condition is for checking Regional Manager 
        $branch = ($request->role == 16 || $request->role == 18 ?  $request->branches[0] : $request->branch);
        if (!empty($request->vdimg)) {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
            $file = $this->uploads($request->vdimg, 'images/users/', $request->prevImg);


            $items = [
                'fullname' => $request->fullname,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'contact' => $request->contact,
                'country_id' => $request->country,
                'city_id' => $request->city,
                'address' => $request->address,
                'image' => !empty($request->vdimg) ? $file["fileName"] :  $request->prevImg,
                // 'remember_token' => null,
                // 'created_at' => $request->createdat,
                'updated_at' => date('Y-m-d H:i:s'),
                'show_password' => $request->password,
            ];
            $user = $users->update_userdetails($request->id, $items);
        } else {
            $items = [
                'fullname' => $request->fullname,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'contact' => $request->contact,
                'country_id' => $request->country,
                'city_id' => $request->city,
                'address' => $request->address,
                // 'remember_token' => null,
                // 'created_at' => $request->createdat,
                'updated_at' => date('Y-m-d H:i:s'),
                'show_password' => $request->password,
            ];
            $user = $users->update_userdetails($request->id, $items);
        }

        $items = [
            'user_id' => $request->id,
            'company_id' => $request->company,
            'branch_id' => $branch,
            'role_id' => $request->role,
            'status_id' => 1,
        ];
        $result = $users->update_user_authorization($request->authid, $items);
        // This condition is for checking Regional Manager
        DB::table("user_branches")->where("user_id", $request->id)->delete();
        if (in_array($request->role, [16, 18])) {
            $count = count($request->branches);
            if ($count > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $items = [
                        'user_id' => $request->id,
                        'branch_id' => $request->branches[$i],
                    ];
                    $result = $users->insert_user('user_branches', $items);
                }
            }
        }


        return redirect('usersDetails');
    }


    public function delete_user(Request $request, userDetails $users)
    {
        $user = User::findOrFail($request->id);
        $result = $users->delete_user($request->id);
        if ($result) {
            $this->removeImage("images/users/", $user->image);
        }
        return 1;
    }

    public function addrole(Request $request, userDetails $users)
    {

        $checkExists = $users->chk_role($request->rolename);
        if ($checkExists == 0) {
            $items = [
                'role' => $request->rolename,
            ];
            $result = $users->addRole($items);
            if ($result == 1) {
                $getRoles = $users->getroles();
                return $getRoles;
            } else {
                return 0;
            }
        } else {
            return 2;
        }
    }

    public function sales_permission(Request $request, userDetails $users)
    {
        $terminal = Crypt::decrypt($request->id);
        $terminal_name = $users->getTerminalName($terminal);
        $result = $users->getPermission($terminal);
        return view('Users.permission', compact('terminal_name', 'terminal', 'result'));
    }

    public function sales_permission_insert(Request $request, userDetails $users)
    {
        $items = [
            'user_id' => session("userid"),
            'terminal_id' => $request->terminalId,
            'ob' => ($request->ob == "on" ? 1 : 0),
            'cb' => ($request->cb == "on" ? 1 : 0),
            'cash_sale' => ($request->cashSales == "on" ? 1 : 0),
            'card_sale' => ($request->cardSales == "on" ? 1 : 0),
            'customer_credit_sale' => ($request->customerCredtSales == "on" ? 1 : 0),
            'cost' => ($request->costing == "on" ? 1 : 0),
            'r_cash' => ($request->r_cash == "on" ? 1 : 0),
            'r_card' => ($request->r_card == "on" ? 1 : 0),
            'r_cheque' => ($request->r_cheque == "on" ? 1 : 0),
            'sale_return' => ($request->saleReturn == "on" ? 1 : 0),
            'discount' => ($request->discounts == "on" ? 1 : 0),
            'cash_in' => ($request->cashIn == "on" ? 1 : 0),
            'cash_out' => ($request->cashOut == "on" ? 1 : 0),
            'customer' => ($request->customer == "on" ? 1 : 0),
            'delivery' => ($request->delivery == "on" ? 1 : 0),
            'retail' => ($request->retail == "on" ? 1 : 0),
            'wholesale' => ($request->wholesale == "on" ? 1 : 0),
            'tables' => ($request->tables == "on" ? 1 : 0),
            'prepayment' => ($request->prepayment == "on" ? 1 : 0),
            'token_print' => ($request->token_print == "on" ? 1 : 0),
            'receipt_1' => ($request->receipt_1 == "on" ? 1 : 0),
            'receipt_2' => ($request->receipt_2 == "on" ? 1 : 0),
            'receipt_3' => ($request->receipt_3 == "on" ? 1 : 0),
            'receipt_4' => ($request->receipt_4 == "on" ? 1 : 0),
            'terminal_auto_sync' => ($request->terminal_auto_sync == "on" ? 1 : 0),
            'goods' => ($request->goods == "on" ? 1 : 0),
            'item_delete_pos' => ($request->item_delete_pos == "on" ? 1 : 0),
            'fbr_sync' => ($request->fbr_sync == "on" ? 1 : 0),
            'stock_deduction' => ($request->stock_deduction == "on" ? 1 : 0),
            'declaration' => ($request->declaration == "" ? 1 : $request->declaration),
            'isdb' => ($request->isdb == "" ? 1 : $request->isdb),
            'cio' => ($request->cio == "" ? 1 : $request->cio),
            'retail_layout' => ($request->retail_layout == "on" ? 1 : 0),
            'restaurant_layout' => ($request->restaurant_layout == "on" ? 1 : 0),
            'print_receipt' => ($request->print_receipt == "" ? 1 : $request->print_receipt),
            'Colors' => ($request->colors == "on" ? 1 : 0),
            'print_to_server' => ($request->print_to_server == "on" ? 1 : 0),
            'print_article_code' => ($request->print_article_code == "on" ? 1 : 0),
            'update_item_name' => ($request->update_item_name == "on" ? 1 : 0),
            'two_inch_printing' => ($request->two_inch_printing == "on" ? 1 : 0),
            'three_inch_printing' => ($request->three_inch_printing == "on" ? 1 : 0),
            'thank_you_voice' => ($request->thank_you_voice == "on" ? 1 : 0),
            'Department_Token_Print' => ($request->department_printing == "on" ? 1 : 0),
        ];

        if ($request->id == 0) {
            $result = $users->createPermission($items);
            $this->sendPushNotification($request->terminalId, "New POS Permission added");
        } else {
            $result = $users->updatePermission($request->id, $items);
            $this->sendPushNotification($request->terminalId, "POS Permission updated");
        }

        return redirect('permission/' . Crypt::encrypt($request->terminalId));
    }

    public function getPermission(Request $request, userDetails $users)
    {
        $result = $users->getPermission($request->id);
        return $result;
    }


    public function changeLoggedInStatus(Request $request)
    {

        // return DB::select("Update user_authorization set isLoggedIn = ? where authorization_id  = ?",[$request->value,$request->id]); 
        if (DB::table("user_authorization")->where("authorization_id", $request->id)->update(["isLoggedIn" => $request->value])) {
            $result = DB::table("user_authorization")->where("authorization_id", $request->id)->get();
            if ($request->value == 0) {
                $this->sendPushNotificationToUserDevice($result[0]->user_id);
            }
            return 1;
        } else {

            return 0;
        }
    }

    public function sendPushNotificationToUserDevice($userId)
    {
        $message = "User has been logout.";
        $tokens = array();
        $title = "Logging out of POS";
        $firebaseToken = DB::table("user_details")->where("id", $userId)->whereNotNull("device_token")->get(); //["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
        foreach ($firebaseToken as $token) {
            array_push($tokens, $token->device_token);
        }

        $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $server_api_key_mobile = 'AAAA2dlOr6s:APA91bHGDpYDSZWI0LotnIYZUTpOTA9lLS56jsyB-2hq6Fsq6l0OPBoMYFqePTAbteVFawWzdyZOfMowMf-j8LBL8xJefdnpb_pZRVQHzu5rXykkdLBfPJgcr8gmPhPBDlXMWJy_-uv2';

        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $title,
                "body" => $firebaseToken[0]->fullname,
                "icon" => "https://retail.sabsoft.com.pk/assets/images/Sabify72.png",
                "content_available" => true,
                "priority" => "high",
                // "click_action" => ,
            ],
            "data" => [
                "par1" => "LG" . $userId,
            ],
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        curl_exec($ch);

        $response = curl_exec($ch);
        // print_r(json_encode($response));
        $headers1 = [
            'Authorization: key=' . $server_api_key_mobile,
            'Content-Type: application/json',
        ];

        $chs = curl_init();

        curl_setopt($chs, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($chs, CURLOPT_POST, true);
        curl_setopt($chs, CURLOPT_HTTPHEADER, $headers1);
        curl_setopt($chs, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chs, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chs, CURLOPT_POSTFIELDS, $dataString);

        curl_exec($chs);
        $response1 = curl_exec($chs);
        // print_r(json_encode($response1));
        return 1; //json_encode($response1)." <br/>".json_encode($response);
    }

    public function sendPushNotification($terminalId, $message)
    {

        $tokens = array();
        // $result = DB::select("SELECT branch_name,b.name as company FROM `branch` INNER Join company b on b.company_id = branch.company_id where branch_id = ?",[session("branch")]);
        $result = DB::select("SELECT c.name as company,b.branch_name as branch_name FROM terminal_details a INNER JOIN branch b on b.branch_id = a.branch_id INNER JOIN company c on c.company_id = b.company_id where terminal_id = ?", [$terminalId]);
        $title = ucwords($result[0]->company) . " (" . ucwords($result[0]->branch_name) . ")";
        $firebaseToken = DB::table("terminal_details")->where("terminal_id", $terminalId)->whereNotNull("device_token")->get("device_token"); //["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"];//User::whereNotNull('device_token')->pluck('device_token')->all();
        foreach ($firebaseToken as $token) {
            array_push($tokens, $token->device_token);
        }
        $SERVER_API_KEY = 'AAAATXdhnIk:APA91bHFZZbCubOgnG3dihDVsqFbwGGQaBpC6f7BPFMnvpntpOOY88ysAEVAT2puQvdng3Xkd8j4HNVWFp1FQ2rHEe9g3Cv6nSZ7oeMsQtSh2GrJYNIxGHeogmen7TSPqRWHJxrG4QF_';

        $data = [
            "registration_ids" => $tokens,
            "notification" => [
                "title" => $title,
                "body" => $message,
                "icon" => "https://retail.sabsoft.com.pk/assets/images/Sabify72.png",
                "content_available" => true,
                "priority" => "high",
                // "click_action" => ,
            ],
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        // return json_encode($response);
    }
}
