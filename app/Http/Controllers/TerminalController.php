<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Terminal;
use App\terminal_print_details;
use App\Traits\MediaTrait;
use App\Services\TerminalLockService;
use App\userDetails;
use App\Models\Terminal as TerminalModel;

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
        $lan_wifi = $request->optionsRadios == "lan_wifi" ? 1 : 0;
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
                $ter->LAN_Wifi = $lan_wifi;
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
            $ter->LAN_Wifi = $lan_wifi;
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

    public function lockTerminal(Request $request, TerminalLockService $terminalLockService)
    {
        $terminalId = (int) $request->terminal_id;
        $result = $terminalLockService->lockTerminalById($terminalId);
        $this->logTerminalLockActivity($terminalId, 'terminal_manual_lock', $result);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'response' => $result['response'] ?? null,
            'lock_password' => $result['lock_password'] ?? null,
        ], $result['status']);
    }

    public function unlockTerminal(Request $request, TerminalLockService $terminalLockService)
    {
        $terminalId = (int) $request->terminal_id;
        $result = $terminalLockService->unlockTerminalById($terminalId);
        $this->logTerminalLockActivity($terminalId, 'terminal_manual_unlock', $result);

        return response()->json($result, $result['status']);
    }

    public function checkTerminalStatus(Request $request, TerminalLockService $terminalLockService)
    {
        $result = $terminalLockService->checkTerminalStatusById((int) $request->terminal_id);

        return response()->json($result, $result['status']);
    }

    public function revealLockPassword(Request $request)
    {
        $terminalId = (int) $request->terminal_id;
        $terminal = TerminalModel::query()
            ->leftJoin('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
            ->where('terminal_details.terminal_id', $terminalId)
            ->select([
                'terminal_details.terminal_id',
                'terminal_details.terminal_name',
                'terminal_details.serial_no',
                'terminal_details.lock_password',
                'terminal_details.is_locked',
                'branch.branch_id',
                'branch.branch_name',
                'branch.company_id',
            ])
            ->first();

        if (!$terminal) {
            return response()->json([
                'status' => 404,
                'message' => 'Terminal not found.',
            ], 404);
        }

        if (empty($terminal->lock_password)) {
            return response()->json([
                'status' => 404,
                'message' => 'No lock password is saved for this terminal.',
            ], 404);
        }

        activity('terminal_lock_password')
            ->causedBy(auth()->user())
            ->withCompany($terminal->company_id)
            ->withBranch($terminal->branch_id)
            ->withProperties([
                'terminal_id' => (int) $terminal->terminal_id,
                'terminal_name' => $terminal->terminal_name,
                'serial_no' => $terminal->serial_no,
                'is_locked' => (int) ($terminal->is_locked ?? 0),
                'revealed_by_user_id' => auth()->id(),
                'revealed_by_username' => auth()->user()->username ?? null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->event('password_revealed')
            ->log('Terminal lock password was revealed for ' . $terminal->terminal_name);

        return response()->json([
            'status' => 200,
            'message' => 'Password revealed.',
            'lock_password' => $terminal->lock_password,
        ]);
    }

    private function logTerminalLockActivity(int $terminalId, string $event, array $result): void
    {
        $terminal = DB::table('terminal_details')
            ->leftJoin('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
            ->where('terminal_details.terminal_id', $terminalId)
            ->select([
                'terminal_details.terminal_id',
                'terminal_details.terminal_name',
                'terminal_details.serial_no',
                'terminal_details.is_locked',
                'branch.branch_id',
                'branch.company_id',
            ])
            ->first();

        $logger = activity('terminal_lock')
            ->causedBy(auth()->user())
            ->withProperties([
                'terminal_id' => $terminalId,
                'terminal_name' => $terminal->terminal_name ?? null,
                'serial_no' => $terminal->serial_no ?? null,
                'is_locked' => $terminal ? (int) ($terminal->is_locked ?? 0) : null,
                'status' => $result['status'] ?? null,
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? null,
                'lock_password' => $result['lock_password'] ?? null,
                'user_id' => auth()->id(),
                'username' => auth()->user()->username ?? null,
            ]);

        if ($terminal && $terminal->company_id) {
            $logger->withCompany($terminal->company_id);
        }

        if ($terminal && $terminal->branch_id) {
            $logger->withBranch($terminal->branch_id);
        }

        $logger->event($event)
            ->log($result['message'] ?? $event);
    }
}
