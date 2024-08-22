<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\floor;


class FloorController extends Controller
{
    public function index(floor $floor)
    {
        $floors = $floor->getFloors();
        return view("floors.list",compact('floors'));
    }

    public function store(Request $request,floor $floor)
    {
        $data = [
            'branch_id' => session('branch'),
            'floor_name'=> $request->get('floorname'),
            'table_qty'=> $request->get('tableQty'),
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        if($floor->check_floor($request->get('deptname'))){
            return response()->json(array("state"=>1,"msg"=>'This department already exists.',"contrl"=>'deptname'));
        }else {
            $result = $floor->insert_floor($data);
            if($result){
                return response()->json(array("state"=>0,"msg"=>'',"contrl"=>''));
            }else{
                return response()->json(array("state"=>1,"msg"=>'Not saved :(',"contrl"=>''));
            }
        }

    }

    public function edit()
    {

    }

    public function update(Request $request,floor $floor)
    {
//        if($floor->check_floor($request->get('floorname'))){
//            return response()->json(array("state"=>1,"msg"=>'This floor-name already exists.',"contrl"=>'tbx_'.$request->get('sdepart')));
//        }else {

            if($floor->modify("floors",['floor_name'=>$request->get('floorname'),'table_qty' => $request->get('tableQty')],['floor_id' => $request->get('floorid')])){
                return response()->json(array('state'=>0,'msg'=>'Saved changes :) '));
            }else {
                return response()->json(array('state'=>1,'msg'=>'Oops! not saved changes :('));
            }

//        }
    }

    public function deleteFloor(Request $request,floor $floor)
    {
        if($floor->deleteFloor($request->get('id'))){
            return 1;
        } else{
            return 2;
        }
    }
}