<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\bonus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;

class BonusController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    } 

    public function view(bonus $bonus){
    $details = $bonus->bonus_details();
    return view('Bonus.show-bonus', compact('details'));	
    }

      public function show(bonus $bonus){
      	$getemp = $bonus->getemployee(session("branch"));
        return view('Bonus.create-bonus', compact('getemp'));	
    }

       public function store(bonus $bonus, Request $request){

      $last_day = date('Y-m-t', strtotime(date('Y-m-d')));
      $first_day = date('Y-m-01', strtotime(date('Y-m-d')));

        $exsist = $bonus->bonus_exsist($request->employee,$first_day,$last_day);
        if ($exsist[0]->counts == 0) {
        $items=[
          'emp_id' => $request->employee,
          'bonus_amt' => $request->bonusamt,
          'bonus_percentage' => $request->bonusper,
          'reason' => $request->reason,
          'status_id' => 1,
        ];
        $bonus = $bonus->insert('bonus_details',$items);
        return 1;
        }
        else{
          return 0;
        }
    }

      public function delete(bonus $bonus, Request $request){
        $result = $bonus->delete_bonus($request->id);
        return $result;
    }

       public function edit(bonus $bonus, Request $request){
        $getemp = $bonus->getemployee(session("branch"));
        $details = $bonus->bonus_details_byid($request->id);
        return view('Bonus.edit-bonus', compact('getemp','details')); 
    }

        public function update(bonus $bonus, Request $request){
   
        $items=[
          'emp_id' => $request->employee,
          'bonus_amt' => $request->bonusamt,
          'bonus_percentage' => $request->bonusper,
          'reason' => $request->reason,
          'status_id' => 1,
        ];
        $bonus = $bonus->update_bonus_details($request->bonusid,$items);
        return 1;
    }
}