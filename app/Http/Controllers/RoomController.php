<?php

namespace App\Http\Controllers;

use App\floor;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(floor $floor)
    {
        $floors = $floor->getFloors();
        $rooms = Room::with('floors')->where('status',1)->get();
        return view("rooms.list", compact('rooms','floors'));
    }

    public function store(Request $request)
    {
        $data = [
            'floor_id' => $request->get('floor_id'),
            'room_no' => $request->get('room_no'),
        ];

        if (Room::where(['room_no' => $request->get('room_no'), 'floor_id' => $request->get('floor_id')])->exists()) {
            return response()->json(array("state" => 1, "msg" => 'This Room already exists.', "contrl" => 'deptname'));
        } else {
            $result = Room::create($data);
            if ($result) {
                return response()->json(array("state" => 0, "msg" => '', "contrl" => ''));
            } else {
                return response()->json(array("state" => 1, "msg" => 'Not saved :(', "contrl" => ''));
            }
        }
    }

    public function edit() {}

    public function update(Request $request)
    {
        $room = Room::where("id",$request->room_id)->update([
            "floor_id" => $request->get('floor_id'),
            "room_no" => $request->get('room_no')
        ]);
        if ($room) {
            return response()->json(array('state' => 1, 'msg' => 'Saved :) '));
        } else {
            return response()->json(array('state' => 0, 'msg' => 'Oops! not saved changes :('));
        }
    }

    public function deleteRoom(Request $request)
    {
        $delete = Room::where("id",$request->get('id'))->update(['status' => 0]);
        if ($delete) {
            return 1;
        } else {
            return 2;
        }
    }
}
