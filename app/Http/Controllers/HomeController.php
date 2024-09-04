<?php

namespace App\Http\Controllers;

use App\dashboard;
use App\User;
use App\Models\Terminal;
use App\Models\Notification;
use App\Models\SalesOpening;
use App\Models\SalesClosing;
use App\userDetails;
use Illuminate\Http\Request;
use flash;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Session;
use Illuminate\Support\Facades\Crypt;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function loginPage(Request $request)
    {
        if (session::has("branch")) {
            return redirect('dashboard');
        } else {

            // $user = User::where('username', $request->input('username'))->first();
            // User::where('id', auth()->user()->id)->update(['isLoggedIn' => 0]);
            return view('auth.login');
        }
    }
    public function index(dashboard $dash, Request $request)
    {
        // return !empty(session("settings")) ? session("settings")['currency'] : 'Rs.';
        // return session("currency");
        $customers = $dash->getCustomersCount();
        $masters = $dash->getMastersCount();
        $vendors = $dash->getVendorsCount();
        $products = $dash->getMostSalesProduct();
        $totalstock = $dash->getTotalItems();
        $months = $dash->getMonthsSales();
        $year = $dash->getYearlySales();
        $orders = $dash->orderStatus();
        $branches = $dash->branches();
        $sales = $dash->sales();
        $monthsales = $dash->monthsales();
        $totalSales = $dash->totalSales();
        $expenseAmount = $dash->totalExpense();
        $vendorPayable = $dash->getVendorPayable();
        $customerPayable = $dash->getCustomerPayable();
        $currentDate = date("Y-m-d");
        $permission = $dash->dashboardRole();
        $projected = $dash->getProjectedSales();
        //        return parse_url($request->url(),PHP_URL_PATH);


        //        $pageid = DB::select('SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id',[session("roleId")]);
        //        $array = [];
        //
        //        foreach ($pageid as $value)
        //        {
        //            array_push($array,$value->page_id);
        //        }
        //
        //        $result = DB::table('pages_details')->whereIN('id',$array)->get();

        return view('dashboard', compact('customers', 'masters', 'vendors', 'products', 'months', 'year', 'totalstock', 'orders', 'branches', 'sales', 'monthsales', 'totalSales', 'expenseAmount', 'vendorPayable', 'customerPayable', 'currentDate', 'permission', 'projected'));
    }

    public function getTerminalsByBranch(Request $request, dashboard $dash)
    {
        $terminals = $dash->getTerminalsByBranch($request->branch, $request->status);
        return $terminals;
    }

    public function getCloseTerminalDeclarationNumber(Request $request, dashboard $dash, userDetails $user)
    {
        $declarations = $dash->getDeclarationsNumber($request->date, $request->terminal);
        return $declarations;
    }

    public function salesDetails(Request $request, dashboard $dash, userDetails $user)
    {
        $branches = $dash->branches();
        $branchesClosedSales = $dash->branchesForClosedSales();
        // $permission = $user->getPermission(1);
        $permission = [];
        // return view('Dashboard.sales', compact('branches', 'permission', 'branchesClosedSales')); // Previous Code
        return view('Dashboard.sales-bootstrap5', compact('branches', 'permission', 'branchesClosedSales'));
    }

    public function salesHead(Request $request, dashboard $dash)
    {
        $heads = $dash->headsDetails($request->terminal);

        if (!empty($heads)) {
            return $heads;
        } else {
            $heads = $dash->lastDayDetails($request->terminal);
            return $heads;
        }
    }

    public function getDetailByMode(Request $request, dashboard $dash)
    {
        //        return Crypt::decrypt($request->id)." Terminal".Crypt::decrypt($request->terminal);
        $branches = $dash->branches();
        $names = $dash->getBranchAndTerminalName(Crypt::decrypt($request->terminal));
        $mode = $request->mode;
        if ($request->mode == "isdb") {
            $details = $dash->isdb(Crypt::decrypt($request->id), Crypt::decrypt($request->terminal), $request->mode);
        } elseif ($request->mode == "ci") {
            $details = $dash->cashIn(Crypt::decrypt($request->id), Crypt::decrypt($request->terminal), $request->mode);
        } elseif ($request->mode == "co") {
            $details = $dash->cashOut(Crypt::decrypt($request->id), Crypt::decrypt($request->terminal), $request->mode);
        } elseif ($request->mode == "sr") {
            $details = $dash->salesReturn(Crypt::decrypt($request->id));
        } elseif ($request->mode == "ex") {
            $details = $dash->expenses(Crypt::decrypt($request->id));
        } else {
            $details = $dash->getDetailsByMode(Crypt::decrypt($request->id), Crypt::decrypt($request->terminal), $request->mode);
        }

        return view('Dashboard.salesdetails', compact('branches', 'mode', 'details', 'names'));
    }


    public function heads(Request $request, dashboard $dash, userDetails $users)
    {

        $result = $users->getPermission($request->terminal);
        $terminal_name = $users->getTerminalName($request->terminal);
        $heads = $dash->headsDetails($request->terminal);

        // if (session("userid") == 710) {
        if (!empty($heads)) {
            return view('Dashboard.partial', compact('heads', 'terminal_name', 'result'));
        } else {
            $heads = $dash->lastDayDetails($request->terminal);
            return view('Dashboard.partial', compact('heads', 'terminal_name', 'result'));
        }
        // } else {
        //     if (!empty($heads)) {
        //         return view('Users.partial', compact('heads', 'terminal_name', 'result'));
        //     } else {
        //         $heads = $dash->lastDayDetails($request->terminal);
        //         return view('Users.partial', compact('heads', 'terminal_name', 'result'));
        //     }
        // }
    }

    public function lastDayHeads(Request $request, dashboard $dash, userDetails $users)
    {

        $result = $users->getPermission($request->terminal);
        $terminal_name = $users->getTerminalName($request->terminal);
        $heads = $dash->getheadsDetailsFromOpeningIdForClosing($request->openingId);
        // return response()->json(["heads" => $heads]);
        return view('Users.partial', compact('heads', 'terminal_name', 'result'));
    }

    public function cheques_notify(dashboard $dash)
    {
        $today = date('Y-m-d');
        $tomorrow = date("Y-m-d", strtotime("+1 days"));
        $data = $dash->getchequesCounts($today, $tomorrow);
        return $data;
    }

    public function getUnseenOrders()
    {
        $orders = DB::select("SELECT id,order_mode_id,b.payment_mode,c.branch_name,d.name as company_name FROM `sales_receipts` INNER JOIN (Select payment_id,payment_mode from sales_payment) as b on b.payment_id = sales_receipts.payment_id INNER JOIN branch c on c.branch_id = sales_receipts.branch INNER JOIN company d on d.company_id = c.company_id WHERE `branch` = ? and order_mode_id != 4 and is_notify = 1 ", [session("branch")]);
        $onlineorders = DB::select("SELECT id,order_mode_id,b.payment_mode,c.branch_name,d.name as company_name FROM `sales_receipts` INNER JOIN (Select payment_id,payment_mode from sales_payment) as b on b.payment_id = sales_receipts.payment_id INNER JOIN branch c on c.branch_id = sales_receipts.branch INNER JOIN company d on d.company_id = c.company_id WHERE `branch` = ? and order_mode_id = 4 and is_notify = 1 ", [session("branch")]);
        return response()->json(["onlineorders" => $onlineorders, "order" => $orders]);
    }

    public function getDueDateOrders()
    {
        $orders = DB::select("SELECT SUM(total_amount) as totalAmount,COUNT(customer_id) as totalCustomers FROM `sales_receipts` WHERE `branch` = ? and due_date = ?", [session("branch"), date("Y-m-d")]);
        return response()->json(["orders" => $orders]);
    }

    public function closeTerminal(Request $request)
    {
        $checkExist = DB::table("sales_closing")->where("opening_id", Crypt::decrypt($request->opening))->count();
        $closingDate = date("Y-m-d");
        $closingTime = date("H:i:s");
        if ($checkExist == 0) {
            $closing = SalesClosing::create([
                "opening_id" => Crypt::decrypt($request->opening),
                "balance" => $request->amount,
                "date" => $closingDate,
                "time" => $closingTime,
            ]);

            if ($closing) {
                $opening = SalesOpening::where("opening_id", Crypt::decrypt($request->opening))->first();
                $terminalData = Terminal::findOrFail($opening->terminal_id);
                DB::table("sales_opening")->where("opening_id", Crypt::decrypt($request->opening))->update(["status" => 2]);
                $message = "C" . $closing->closing_id . ",O" . Crypt::decrypt($request->opening) . ",B" . $request->amount . ",ID" . auth()->user()->id . ",U" . auth()->user()->fullname . ",T" . $opening->terminal_id;
                Notification::sendNotification($terminalData, $message, "Closing", $closingDate, $closingTime);
                return 1;
            } else {
                return 0;
            }
        } else {
            return 2;
        }
    }

    public function salesOpening(Request $request)
    {
        try {
            $terminal = $request->terminal;
            $terminalData = Terminal::with("branch", "branch.company")->where("terminal_id", $request->terminal)->first();
            $checkExist = DB::table("sales_opening")->where("user_id", $terminalData->branch_id)->where("terminal_id", $terminal)->where("status", 1)->count();
            $openingDate = date("Y-m-d");
            $openingTime = date("H:i:s");
            if ($checkExist == 0) {
                $opening = SalesOpening::create([
                    "user_id" => $terminalData->branch_id,
                    "balance" => $request->amount,
                    "status" => 1,
                    "date" => $openingDate,
                    "time" => $openingTime,
                    "terminal_id" => $terminal,
                ]);
                if (!empty($opening)) {
                    $message = "O" . $opening->opening_id . ",B" . $opening->balance . ",ID" . auth()->user()->id . ",U" . auth()->user()->fullname . ",T" . $opening->terminal_id;
                    Notification::sendNotification($terminalData, $message, "Opening", $openingDate, $openingTime);
                    return 1;
                } else {
                    return 0;
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function saveToken(Request $request)
    {
        DB::table("user_details")->where("id", auth()->user()->id)->update(['device_token' => $request->token]);
        return response()->json(['token saved successfully.', "token" => auth()->user()]);
    }

    public function sendNotification(Request $request)
    {
        // return auth()->user()->branch;
        $request->title = "Sabsoft";
        $request->body = "Received From Laravel";
        $firebaseToken = ["cZIiT3EPTAKce8s8lPHTkZ:APA91bH0a0zModJDvMjwLmeMIqHNfyLriX1m2EWV9BI157KY6DtxsfWPDo-mYjl-Qh92dyfjU0Q0BM_HeXykZp6xy3LxoOxZmeLIxyBTimnfsCVIOuM0PBE8j53EV-_AWi6CVMOJIDMH"]; //User::whereNotNull('device_token')->pluck('device_token')->all();


        // $SERVER_API_KEY = 'AAAAgRgRcP0:APA91bEoVPcoBab8_4Crx1rbZbhX-o_hAe9HXq7xNHaSxfo-uQyH2BYXU0sR43SODDgLVFwTYJ37FVE-jnhrSyZoAC78Gjk9lfbftqZA2u5F6MZPNGxsaj3WnIc89pEZ37iiJKVG0Slz';
        $SERVER_API_KEY = 'AAAA_rRLDk4:APA91bE6JCfziNvUz9-sMqJM7M_9eX6W9_XTx10uXNhVPoYAR3LxYTl1lwGpk9euoBlNPLWmsQzo4h9ds0gQH2C-GGCOUZKSRd8LuqknxTggs6livc0sWO-SIeegdFoHPYTtqY2SSoXT';

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,
                "icon" => "https://retail.sabsoft.com.pk/assets/images/desktop-notify-icon.png",
                "content_available" => true,
                "priority" => "high",
                // "click_action" => ,
            ]
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

        dd($response);
    }

    public function logout()
    {
        \Auth::logout();
        return redirect('login');
    }
}
