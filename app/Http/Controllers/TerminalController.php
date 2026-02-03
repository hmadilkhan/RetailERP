<?php

namespace App\Http\Controllers;

use App\Facades\Sunmi;
use App\Models\Terminal as ModelsTerminal;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Terminal;
use App\terminal_print_details;
use App\Traits\MediaTrait;
use App\userDetails;

class TerminalController extends Controller
{
    use MediaTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view(terminal $terminal, Request $request)
    {
        $getbranch = $terminal->getbranches();
        $terminals = $terminal->getterminals(Crypt::decrypt($request->id));
        $branch = Crypt::decrypt($request->id);
        return view('Terminal.create-terminal', compact('getbranch', 'terminals', 'branch'));
    }


    public function store(terminal $terminal, Request $request, userDetails $users)
    {

        $chk = $terminal->exsist_chk($request->terminalname, $request->macaddress, $request->branch);
        if ($chk[0]->counts == 0) {

            $items = [
                'branch_id' => $request->branch,
                'terminal_name' => $request->terminalname,
                'mac_address' => $request->macaddress,
                'serial_no' => $request->serial_no,
                'model_no' => $request->modelno,
                'status_id' => 1,
            ];

            $result = $terminal->insert('terminal_details', $items);

            $items = [
                'user_id' => session("userid"),
                'terminal_id' => $result,
                'ob' =>  1,
                'cb' => 1,
                'cash_sale' =>  1,
                'card_sale' => 1,
                'customer_credit_sale' =>  1,
                'cost' =>  1,
                'r_cash' => 1,
                'r_card' => 1,
                'r_cheque' =>  1,
                'sale_return' =>  1,
                'discount' =>  1,
                'cash_in' => 1,
                'cash_out' =>  1,
            ];


            $result = $users->createPermission($items);



            return $result;
        } else {
            return 0;
        }
    }


    public function remove(terminal $terminal, Request $request)
    {

        $items = [
            'status_id' => 2,
        ];
        $result = $terminal->update_terminal($request->terminalid, $items);
        return $result;
    }

    public function inactivedetails(terminal $terminal, Request $request)
    {
        $result = $terminal->getterminals_inactive($request->id);
        return $result;
    }


    public function reactive(terminal $terminal, Request $request)
    {

        $items = [
            'status_id' => 1,
        ];
        $result = $terminal->update_terminal($request->terminalid, $items);
        return $result;
    }

    public function update(terminal $terminal, Request $request)
    {

        $items = [
            'branch_id' => $request->branch,
            'terminal_name' => $request->terminalname,
            'mac_address' => $request->macaddress,
            'serial_no' => $request->serial_no,
            'model_no' => $request->modelno,
            'status_id' => 1,
        ];
        $result = $terminal->update_terminal($request->terminalid, $items);
        return $result;
    }

    public function getPrintingDetails(terminal $terminal, Request $request)
    {
        $terminals = $terminal->printingDetails(Crypt::decrypt($request->id));
        $terminal_id = Crypt::decrypt($request->id);
        return view('Terminal.terminal-print', compact('terminals', 'terminal_id'));
    }

    public function storePrintDetails(terminal $terminal, Request $request)
    {
        $imageName = "";
        //        $rules = [
        //            'header' => 'required',
        //            'footer' => 'required',storePrint
        //            'printerName'=>'required',
        //            'optionsRadios'=>'required',
        //        ];
        //        $this->validate($request, $rules);

        $lan = $request->optionsRadios == "lan" ? 1 : 0;
        $bluetooth = $request->optionsRadios == "bluetooth" ? 1 : 0;
        $pts = $request->optionsRadios == "pts" ? 1 : 0;
        $desktop = $request->optionsRadios == "desktop" ? 1 : 0;
        $cloud = $request->optionsRadios == "cloud" ? 1 : 0;

        if ($request->mode == "insert") {

            if (count(terminal_print_details::where('terminal_id', $request->terminal_id)->get()) == 0) {
                if (!empty($request->image)) {
                    $request->validate([
                        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);
                    // $imageName = time() . '.' . $request->image->getClientOriginalExtension();

                    // $request->image->move(public_path('assets/images/receipt/'), $imageName);

                    $file = $this->uploads($request->image, 'images/receipt/');
                    $imageName =  !empty($file) ? $file["fileName"] :  '';
                }

                $header_text = $request->header;
                $arr = str_split($header_text, "42"); // break string in 3 character sets
                $header = implode("|", $arr);  // implode array with comma

                $footer_text = $request->footer;
                $arr = str_split($footer_text, "42"); // break string in 3 character sets
                $footer = implode("|", $arr);  // implode array with comma

                $ter = new terminal_print_details;
                $ter->terminal_id = $request->terminal_id;
                $ter->header = $header;
                $ter->footer = $footer;
                $ter->LAN = $lan;
                $ter->bluetooth = $bluetooth;
                $ter->pts = $pts;
                $ter->desktop = $desktop;
                $ter->cloud = $cloud;
                $ter->printer_name = $request->printerName;
                $ter->image = $imageName;
                if ($ter->save()) {
                    return redirect("/printing-details/" . Crypt::encrypt($request->terminal_id))->with('message', 'Printer Setting Saved successfully!!!');
                } else {
                    return redirect("/printing-details/" . Crypt::encrypt($request->terminal_id))->with('message', 'Failed to save settings!!!');
                }
            } else {
                return redirect("/printing-details/" . Crypt::encrypt($request->terminal_id))->with('message', 'Setting already exists. please update !!!');
            }
        } else {

            if (!empty($request->image)) {

                // $image_path = public_path('assets/images/receipt/' . $request->previous_image);  // Value is not URL but directory file path
                // if ($request->previmage != "") {
                //     unlink($image_path);
                // }
                // STORE THE NEW  IMAGE //
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                // REMOVE THE PREVIOUS IMAGE //
                $this->removeImage("images/receipt/", $request->previous_image);

                // $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                // $request->image->move(public_path('assets/images/receipt/'), $imageName);
                $file = $this->uploads($request->image, 'images/receipt/');
                $imageName =  !empty($file) ? $file["fileName"] :  '';
            } else {
                $imageName = $request->previous_image;
            }

            $ter = terminal_print_details::find($request->print_id);
            $ter->terminal_id = $request->terminal_id;
            $ter->header = $request->header;
            $ter->footer = $request->footer;
            $ter->LAN = $lan;
            $ter->bluetooth = $bluetooth;
            $ter->pts = $pts;
            $ter->desktop = $desktop;
            $ter->cloud = $cloud;
            $ter->printer_name = $request->printerName;
            $ter->image = !empty($request->image) ? $imageName :  $request->previous_image;
            if ($ter->save()) {
                return redirect("/printing-details/" . Crypt::encrypt($request->terminal_id))->with('message', 'Update Printer Setting Saved successfully!!!');
            } else {
                return redirect("/printing-details/" . Crypt::encrypt($request->terminal_id))->with('message', 'Failed to save settings!!!');
            }
        }
    }

    public function bindTerminals(terminal $terminal, Request $request)
    {
        $terminals = $terminal->getterminals(Crypt::decrypt($request->branch));
        $branch = Crypt::decrypt($request->branch);
        $terminalID = Crypt::decrypt($request->id);
        $bindTerminals = $terminal->getBindTerminals($terminalID);
        $terminal_name = $terminal->getTerminalName($terminalID);
        return view('Terminal.bind-terminal', compact('terminals', 'branch', 'terminalID', 'bindTerminals', 'terminal_name'));
    }

    public function saveBindTerminal(terminal $terminal, Request $request)
    {
        if ($terminal->chkAlreadyExistsBindTerminal($request->terminal) > 0) {
            return redirect()->back();
        } else {
            $save = $terminal->saveBindTerminal($request->terminalID, $request->terminal);
            if ($save) {
                return redirect()->back();
            }
        }
    }

    public function deleteBindTerminal(terminal $terminal, Request $request)
    {
        $save = $terminal->deleteBindTerminal($request->id);
        if ($save) {
            return 1;
        } else {
            return 0;
        }
    }

    private function parseSunmiResponse($response)
    {
        if(isset($response['http_code']) && $response['http_code'] == 200 && isset($response['raw'])) {
            preg_match_all('/{[^{}]*(?:{[^{}]*}[^{}]*)*}/', $response['raw'], $matches);
            if(!empty($matches[0])) {
                $lastJson = end($matches[0]);
                $decoded = json_decode($lastJson, true);
                if($decoded !== null) {
                    return $decoded;
                }
            }
        }
        return ['code' => 0];
    }

    public function lockTerminal(Request $request)
    {
        $terminal = ModelsTerminal::where("terminal_id",$request->terminal_id)->pluck("serial_no");
        $lock = Sunmi::lock([
            'passwd'     => '03008288',
            'screen_tip' => 'Device Locked',
            'expire_day' => 7,
            'msn_list'   => $terminal,
        ]);
        
        $rawData = $this->parseSunmiResponse($lock);
        if($rawData && isset($rawData['code']) && $rawData['code'] == 1) {
            ModelsTerminal::where("terminal_id",$request->terminal_id)->update(['is_locked' => 1]);
            return response()->json(['status' => 200, 'message' => 'Device locked successfully']);
        }
        
        return response()->json(['status' => 500, 'message' => 'Failed to lock device']);
    }

    public function unlockTerminal(Request $request)
    {
        $terminal = ModelsTerminal::where("terminal_id",$request->terminal_id)->pluck("serial_no");
        $unlock = Sunmi::unlock([
            'msn_list' => $terminal,
        ]);
        
        $rawData = $this->parseSunmiResponse($unlock);
        if($rawData && isset($rawData['code']) && $rawData['code'] == 1) {
            ModelsTerminal::where("terminal_id",$request->terminal_id)->update(['is_locked' => 0]);
            return response()->json(['status' => 200, 'message' => 'Device unlocked successfully']);
        }
        
        return response()->json(['status' => 500, 'message' => 'Failed to unlock device']);
    }

    public function checkTerminalStatus(Request $request)
    {
        $terminal = ModelsTerminal::where("terminal_id",$request->terminal_id)->pluck("serial_no");
        $status = Sunmi::status([
            'msn_list' => $terminal,
        ]);

        // $rawData = $this->parseSunmiResponse($status);
        if( isset($status) && $status["http_code"] == 200 && isset($status['data']) && $status['data']['code'] == 1) {
            return response()->json([
                'status' => 200,
                'message' => 'Device status fetched successfully',
                'data' => $status,
            ]);
        }

        return response()->json(['status' => 500, 'message' => 'Failed to fetch device status',"data" => $status] );
    }
}
