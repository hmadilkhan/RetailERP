<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\bank;
use App\KitchenDepartment;




class KitchenDepartmentController extends Controller
{
    public function index(KitchenDepartment $kitchenDepartment)
    {
        $departments = $kitchenDepartment->getDepartments(session('company_id'));
        $general =  $kitchenDepartment->getgeneral();
        $details =  $kitchenDepartment->getdetails();
        return view("Kitchen_Departments.list",compact('departments','details','general'));
    }

    public function store(Request $request,KitchenDepartment $kitchenDepartment)
    {
        $items = [
            'kitchen_department_name' => $request->deptname,
            'branch_id' => session("branch")
        ];
        $general = $kitchenDepartment->insert("kitchen_departments_general",$items);

        foreach ($request->depart as $value)
        {
            $details = [
                'kitchen_depart_id' => $general,
                'inventory_department_id' => $value
            ];
            $result = $kitchenDepartment->insert("kitchen_department_details",$details);
        }

        return $general;
    }

    public function printers(Request $request,KitchenDepartment $kitchenDepartment)
    {
        $printers = $kitchenDepartment->getPrinters($request->id);
        $department_id = $request->id;
        return view("Kitchen_Departments.printers",compact('printers','department_id'));
    }

    public function storePrinters(Request $request,KitchenDepartment $kitchenDepartment)
    {
        $lan = $request->optionsRadios == "lan" ? 1 : 0;
        $bluetooth = $request->optionsRadios == "bluetooth" ? 1 : 0;
		$desktop = $request->optionsRadios == "desktop" ? 1 : 0;
		$cloud = $request->optionsRadios == "cloud" ? 1 : 0;

        if($request->mode == "insert") {
            $items = [
                'department_id' => $request->department_id,
                'printer_name' => $request->printerName,
                'LAN' => $lan,
                'bluetooth' => $bluetooth,
				'Desktop' => $desktop,
				'cloud' => $cloud,
            ];
            $result = $kitchenDepartment->insert("kitchen_department_printers", $items);
            if ($result) {
                return redirect()->back();
            }
        }else{
            $items = [
                'printer_name' => $request->printerName,
                'LAN' => $lan,
                'bluetooth' => $bluetooth,
				'Desktop' => $desktop,
				'cloud' => $cloud,
            ];

            $result = $kitchenDepartment->modifyPrinter("kitchen_department_printers", $items,$request->print_id);
//            if ($result) {
                return redirect()->back();
//            }else{
//                return $request->print_id;
//            }
        }
    }

    public function updatedepart(KitchenDepartment $kitchenDepartment, Request $request)
    {
        $items = [
            'kitchen_department_name' => $request->departname,
        ];
        $result = $kitchenDepartment->update_depart("kitchen_departments_general", $items,$request->departid);
        return $result;

    }

    public function getKitchenDepart(KitchenDepartment $kitchenDepartment, Request $request)
    {
        $result = $kitchenDepartment->getKitchenDepart($request->departid);
        return $result;
    }

    public function updateKitchenSubDepartment(KitchenDepartment $kitchenDepartment, Request $request)
    {
        if (!empty($request->kdepartment)){
            $result = DB::table("kitchen_department_details")->where("kitchen_depart_id",$request->uhidd_id)->delete();
            if($result){
                foreach ($request->kdepartment as $val){
                    $details = [
                        'kitchen_depart_id' => $request->uhidd_id,
                        'inventory_department_id' => $val
                    ];
                    $result = $kitchenDepartment->insert("kitchen_department_details",$details);
                }
                return 1;
            }
        }else{
            return 2;
        }


    }
}