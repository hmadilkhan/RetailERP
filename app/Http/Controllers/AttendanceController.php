<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\attendance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;
use PDF;
use Crabbly\Fpdf\Fpdf;


class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function shiftview(attendance $attendance){
        $getshifts = $attendance->getshifts();
        return view('Attendance.shift-view', compact('getshifts'));
    }

    public function shiftshow(attendance $attendance){
        $getbranch = $attendance->getbranch();
        return view('Attendance.shift-create', compact('getbranch'));
    }

    public function shiftedit(attendance $attendance, request $request){
        $getbranch = $attendance->getbranch();
        $details = $attendance->getshiftdetails($request->id);
        return view('Attendance.shift-edit', compact('getbranch','details'));
    }

    public function shiftinsert(attendance $attendance, request $request){

        $rules = [
            'branch' => 'required',
            'shiftname' => 'required',
            'shiftstart' => 'required',
            'shiftend' => 'required',
        ];
        $this->validate($request, $rules);
        if ($request->chkbox == "on") {
            $startdate = date("Y-m-d");
            $enddate = new DateTime('+1 day');
            $enddate = $enddate->format('Y-m-d');

        }
        else{
            $startdate = date("Y-m-d");
            $enddate = date("Y-m-d");
        }

        $starttime = date("H:i:s", strtotime($request->shiftstart));
        $endtime = date("H:i:s", strtotime($request->shiftend));
        $start  = new Carbon($startdate.' '.$starttime);
        $end    = new Carbon($enddate.' '.$endtime);

        $atttime = $start->diff($end)->format('%H');

        $items = [
            'shiftname' => $request->shiftname,
            'shift_start' => $request->shiftstart,
            'shift_end' => $request->shiftend,
            'branch_id' => $request->branch,
            'grace_time_in' => $request->gracetime,
            'grace_time_out' => $request->gracetimeearly,
            'ATT_time' => $atttime,
        ];
        $shift = $attendance->insert('office_shift',$items);
        return redirect('/view-shift');

    }

    public function deleteshift(attendance $attendance, request $request){
        $result = $attendance->shift_delete($request->id);
        return $result;
    }

    public function shiftupdate(attendance $attendance, request $request){

        $rules = [
            'branch' => 'required',
            'shiftname' => 'required',
            'shiftstart' => 'required',
            'shiftend' => 'required',
        ];
        $this->validate($request, $rules);

        $atttime = round(abs(strtotime($request->shiftend)  - strtotime($request->shiftstart))/60,2);
        $atttime = $atttime / 60;

        $items = [
            'shiftname' => $request->shiftname,
            'shift_start' => $request->shiftstart,
            'shift_end' => $request->shiftend,
            'branch_id' => $request->branch,
            'grace_time_in' => $request->gracetime,
            'grace_time_out' => $request->gracetimeearly,
            'ATT_time' => $atttime,
        ];

        $shift = $attendance->shift_update($request->shiftid, $items);
        return redirect('/view-shift');

    }

    public function OTtview(attendance $attendance){
        $getot = $attendance->getotformula();
        return view('Attendance.ot-view', compact('getot'));
    }

    public function OTshow(attendance $attendance){

        return view('Attendance.ot-create', compact(0));
    }

    public function otinsert(attendance $attendance, request $request){

        $exsit = $attendance->ot_exsit_chk($request->otformula);
        if ($exsit[0]->counts == 0) {
            $rules = [
                'otformula' => 'required',
            ];
            $this->validate($request, $rules);
            $items = [
                'OTFormula' => $request->otformula,
            ];
            $ot = $attendance->insert('overtime_formula',$items);
            if ($request->chk == 1) {
                $getot = $attendance->getotformula();
                return $getot;
            }
            else{
                return redirect('/view-ot');
            }
        }
        else{
            return 0;
        }
    }

    public function otamountinsert(attendance $attendance, request $request){

        $exsit = $attendance->otamt_exsit_chk($request->otamount);
        if ($exsit[0]->counts == 0) {
            $items = [
                'amount' => $request->otamount,
            ];
            $ot = $attendance->insert('overtime_amount',$items);
            $otamount = $attendance->getotamount();
            return $otamount;
        }
        else{
            return 0;
        }
    }

    public function otdurationinsert(attendance $attendance, request $request){

        $exsit = $attendance->otduration_exsit_chk($request->duration);
        if ($exsit[0]->counts == 0) {
            $items = [
                'duration' => $request->duration,
            ];
            $ot = $attendance->insert('overtime_duration',$items);
            $otduration = $attendance->getotduration();
            return $otduration;
        }
        else{
            return 0;
        }
    }

    public function otedit(attendance $attendance, request $request){
        $details = $attendance->getotformulabyid($request->id);
        return view('Attendance.ot-edit', compact('details'));
    }

    public function otupdate(attendance $attendance, request $request){

        $rules = [
            'otformula' => 'required',
        ];
        $this->validate($request, $rules);

        $items = [
            'OTFormula' => $request->otformula,
        ];
        $ot = $attendance->ot_update($request->otformulaid, $items);
        return redirect('/view-ot');

    }

    public function deleteot(attendance $attendance, request $request){
        $result = $attendance->ot_delete($request->id);
        return $result;
    }

    public function attendanceview(attendance $attendance,request $request){
        $date = date('Y-m-d');
        $details = $attendance->getattendancedetails($date);
        $getpresent = $attendance->getpresentemp($date);

        $getabsent = $attendance->getabsent($date);
        return view('Attendance.dailyattendance', compact('details','getpresent','getabsent'));
    }

    public function attendanceedit(attendance $attendance,request $request){
        $getbranch = $attendance->getbranch();
        return view('Attendance.edit-attendance', compact('getbranch'));
    }

    public function show(attendance $attendance,request $request){
        $getbranch = $attendance->getbranch();
        return view('Attendance.add-attendance', compact('getbranch'));
    }

    public function getemployees(attendance $attendance,request $request){
        $employee = $attendance->getemployee($request->branchid);
        return $employee;
    }


    public function getattendetails(attendance $attendance,request $request){
		
		$sheet = $attendance->attendance_sheet($request->branchid,$request->empid,$request->date);
		if(count($sheet) > 0){
			$item = [
				"attendance_id" => $sheet[0]->attendance_id,
				"Atttime" => $sheet[0]->Atttime,
				"branch_id" => $sheet[0]->branch_id,
				"clock_in" => date("H:i",strtotime($sheet[0]->clock_in)),
				"clock_out" => date("H:i",strtotime($sheet[0]->clockout)),
				"date" => $sheet[0]->date,
				"earlys" => $sheet[0]->earlys,
				"emp_acc" => $sheet[0]->emp_acc,
				"emp_id" => $sheet[0]->emp_id,
				"emp_name" => $sheet[0]->emp_name,
				"emp_picture" => $sheet[0]->emp_picture,
				"lates" => $sheet[0]->lates,
				"ot" => $sheet[0]->ot,
			];
			return $item;
		}else{
			return 0;
		}
        //check absent data
        $absent = $attendance->chk_absent($request->empid,$request->date);

        $getholiday = $attendance->get_holiday();

        $holiday = $attendance->chk_holiday($request->date);
        if (count($holiday) == 0) {
            $first_day = date('Y-m-01', strtotime($request->date));
            $generatemonthdata = $attendance->generate_month($first_day);
        }

        $event = $attendance->chk_events($request->date);

        if ($absent[0]->absent != 0) {
            return 1;
        }
        else if ($event[0]->event != 0) {
            return 3;
        }
        else{
            if (count($getholiday) != 0 )
            {

                foreach ($getholiday as $value)
                {
                    if ($value->day_off == $holiday[0]->day){
                        return 2;
                    }
                }
            }
            else{
                $details = $attendance->getdetails($request->branchid,$request->empid,$request->date);
                return $details;
            }

        }
    }

    public function getgracetime(attendance $attendance,request $request){
        $gracetime = $attendance->getgracetime($request->empid);
        return $gracetime;
    }

    public function attendanceupdate(attendance $attendance, request $request){

        if ($request->mode == 1) {


            $atttime = round(abs(strtotime($request->clockout)  - strtotime($request->clockin))/60,2);
            $atttime = $atttime / 60;
            $atttime = gmdate("H:i", floor($atttime * 3600));
            $items = [
                'clock_in' => $request->clockin,
                'clock_out' => $request->clockout,
                'late' => $request->late,
                'early' => $request->early,
                'OT_time' => $request->ot,
                'ATT_time' => $atttime,
            ];


            $result = $attendance->dailyattendance_update($request->attendanceid, $items);
            return 1;
        }
        else{
            $rules = [
                'branch' => 'required',
                'employee' => 'required',
                'attendancedate' => 'required',
                'clockin' => 'required',
                'clockout' => 'required',
            ];
            $this->validate($request, $rules);

            $atttime = round(abs(strtotime($request->clockout)  - strtotime($request->clockin))/60,2);
            $atttime = $atttime / 60;

            $items = [
                'clock_in' => $request->clockin,
                'clock_out' => $request->clockout,
                'late' => $request->late,
                'early' => $request->early,
                'OT_time' => $request->ot,
                'ATT_time' => $atttime,
            ];


            $result = $attendance->dailyattendance_update($request->attendanceid, $items);

            return redirect('/dailyattendance-edit');
        }

    }


    public function getdata(attendance $attendance,request $request){
        $date = date('Y-m-d');
        if ($request->mode == 'Absent') {
            $absent = $attendance->getabsent($date,$request->branchid);
            return $absent;
        }
        else if ($request->mode == 'Present') {
            $present = $attendance->getpresent($date,$request->branchid);
            return $present;
        }
        else if ($request->mode == 'Late') {
            $late = $attendance->getlate($date,$request->branchid);
            return $late;
        }
    }

    public function uploadattendance(attendance $attendance, request $request){

        $result = $attendance->getattendance();
        foreach ($result as $value) {
            $exsit = $attendance->upload_exsist_chk($value->employeeid);
            if ($exsit[0]->counter == 0) {
                // foreach ($result as $value) {
                $late = 0;
                $early = 0;
                $ot = 0;
                if ($value->late > 0) {
                    $late =  $value->late;
                }
                else if ($value->late < 0) {
                    $late = 0;
                }
                else{
                    $late = 0;
                }
                if ($value->early > 0) {
                    $early =  $value->early;
                }
                else if ($value->early < 0) {
                    $early = 0;
                }
                else{
                    $early = 0;
                }
                if ($value->overtime > 0) {
                    $ot = $value->overtime;
                }
                else if ($value->overtime < 0) {
                    $ot = 0;
                }
                else{
                    $ot = 0;
                }

                $items = [
                    'emp_id' => $value->employeeid,
                    'branch_id' =>$value->branchid,
                    'date' =>$value->dateIN,
                    'clock_in' => $value->ClockIn,
                    'clock_out' => $value->clockOut,
                    'late' => $late,
                    'early' => $early,
                    'OT_time' =>$ot,
                    'ATT_time' => $value->ATT_time,
                ];
                $upload = $attendance->insert('attendance_details',$items);
                // }
                // return 1;
            }

            else{
                // foreach ($result as $value) {
                $early = 0;
                $ot = 0;
                if ($value->early > 0) {
                    $early =  $value->early;
                }
                else if ($value->early < 0) {
                    $early = 0;
                }
                else{
                    $early = 0;
                }
                if ($value->overtime > 0) {
                    $ot = $value->overtime;
                }
                else if ($value->overtime < 0) {
                    $ot = 0;
                }
                else{
                    $ot = 0;
                }
                $items = [
                    'clock_out' => $value->clockOut,
                    'early' =>  $early,
                    'OT_time' => $ot,
                    'ATT_time' => $value->ATT_time,
                ];
                $clockout = $attendance->clockout_update($value->employeeid ,$items);
                // }
                // return 0;
            }
        }
        return 1;
    }

    public function getsheet(attendance $attendance,request $request){
        $sheet = $attendance->attendance_sheet($request->branchid,$request->empid);
        return $sheet;
    }

    public function store(attendance $attendance,request $request){
        $rules = [
            'branch' => 'required',
            'employee' => 'required',
            'attendancedate' => 'required',
            'clockin' => 'required',
            'clockout' => 'required',
        ];
        $this->validate($request, $rules);

        $atttime = round(abs(strtotime($request->clockout)  - strtotime($request->clockin))/60,2);
        $atttime = $atttime / 60;
        $atttime = gmdate("H:i", floor($atttime * 3600));
        $items = [
            'emp_id'=> $request->employee,
            'branch_id'=> $request->branch,
            'date'=> $request->attendancedate,
            'clock_in' => $request->clockin,
            'clock_out' => $request->clockout,
            'late' => $request->late,
            'early' => $request->early,
            'OT_time' => $request->ot,
            'ATT_time' => $atttime,
        ];
        $result = $attendance->insert('attendance_details', $items);
        return redirect('/dailyattendance-view');
    }


    public function attendancesheet_pdf(attendance $attendance,request $request){

        $company = $attendance->getcompany();

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);

        $pdf->Image(public_path('assets/images/company/'.$company[0]->logo),10,10,-800);
        // $pdf->SetFont('Arial','BU',18);
        // $pdf->MultiCell(0,10,$company[0]->name,0,'C');
        // $pdf->Cell(2,2,'',0,1);
        // $pdf->SetFont('Arial','B',12);
        // $pdf->Cell(0,3,'Attendance Sheet',0,1,'C'); //Here is center title
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(10,10,'',0,1);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,35,$company[0]->name,0,1,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,-25,$company[0]->address,0,1,'L');
        $pdf->Cell(0,35,'Karachi, Karachi City, Sindh',0,1,'L');
        $pdf->Cell(0,-25,$company[0]->ptcl_contact,0,1,'L');
        $pdf->Cell(0,10,'',0,1,'R');
        $pdf->Cell(190,5,'','',1);//SPACE
        // $pdf->ln();

        $pdf->Cell(190,1,'','T',1);//SPACE

        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,8,'ATTENDANCE SHEET',0,1,'C'); //Here is center title
        $pdf->Cell(190,2,'','T',1);//SPACE


        // $pdf->SetFont('Arial','B',10);
        //   $pdf->setFillColor(230,230,230);
        //   $pdf->Cell(190,7,'Apply Filters',0,1,'L',1);

        //   $pdf->SetFont('Arial','B',10);
        //   $pdf->Cell(50,7,'From Date: 2020-04-20',0,0,'L');
        //   $pdf->Cell(50,7,'To Date: 2020-04-20',0,0,'L');
        //   $pdf->Cell(50,7,'Branch Name: Head Office',0,1,'L');

        //   $pdf->Cell(190,3,'','',1);//SPACE

        $pdf->SetFont('Arial','B',11);
        $pdf->setFillColor(0,0,0);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(46,8,"Employee Name",0,0,'C',1);
        $pdf->Cell(24,8,"Date",0,0,'C',1);
        $pdf->Cell(24,8,"Clock In",0,0,'C',1);
        $pdf->Cell(24,8,"Clock Out",'0',0,'C',1);
        $pdf->Cell(18,8,"Late",'0',0,'C',1);
        $pdf->Cell(18,8,"Early",'0',0,'C',1);
        $pdf->Cell(18,8,"OverTime",'0',0,'C',1);
        $pdf->Cell(18,8,"ATT.Hrs",'0',1,'C',1);
        $pdf->Cell(190,1,'','',1);//SPACE


        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        // $details = $salary->salary_details('','',"","");
        $sheet = $attendance->attendance_sheet($request->branchid,$request->empid);
        foreach ($sheet as $value) {

            $pdf->Cell(46,7,$value->emp_name,0,0,'L');
            $pdf->Cell(24,7,$value->date,0,0,'L');
            $pdf->Cell(24,7,$value->clock_in,0,0,'C');
            $pdf->Cell(24,7,$value->clockout,0,0,'C');
            $pdf->Cell(18,7,$value->lates,0,0,'C');
            $pdf->Cell(18,7,$value->earlys,0,0,'C');
            $pdf->Cell(18,7,$value->ot,0,0,'C');
            $pdf->Cell(18,7,$value->Atttime,0,1,'C');
        }

        $pdf->Cell(190,5,'','',1);//SPACE

        // $pdf->SetFont('Arial','B',11);
        // $pdf->setFillColor(230,230,230);
        // $pdf->Cell(60,7,'Total Present',0,0,'L',1);
        // $pdf->Cell(1,7,'5',0,1,'R',1);

        // $pdf->Cell(190,2,'','',1);//SPACE

        // $pdf->Cell(60,7,'Total Absent',0,0,'L',1);
        // $pdf->Cell(1,7,'5',0,1,'R',1);

        // $pdf->Cell(190,2,'','',1);//SPACE

        // $pdf->Cell(60,7,'Total Late',0,0,'L',1);
        // $pdf->Cell(1,7,'5',0,1,'R',1);

        $pdf->Cell(190,10,'','',1);//SPACE

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(185,4,'This is computer generated report no signature required',0,1,'L');



        $pdf->Output('test.pdf', 'I');
    }

    public function insertabsent(attendance $attendance,request $request){
        $exsist = $attendance->absent_exsist($request->employee,$request->attendancedate);
        if ($exsist[0]->counts == 0) {

            $items = [
                'acc_no'=> $request->employee,
                'absent_date'=> $request->attendancedate,
                'weekday'=> 0,
                'event' => 0,
            ];
            $result = $attendance->insert('absent_details', $items);
            return 1;
        }
        else{
            return 0;
        }

    }

    public function deleteabsent(attendance $attendance,request $request){
        $result = $attendance->absent_delete($request->empid,$request->date);
        return $result;
    }


    public function absentdetails(attendance $attendance){
        $details = $attendance->get_absent_details('','','','');
        $branch = $attendance->getbranch();
        $employee =[];// $attendance->getemployees(session('branch'));
        return view('Attendance.absent-details', compact('details','branch','employee'));
    }

    public function absentfilter(attendance $attendance, request $request){
        $details = $attendance->get_absent_details($request->branchid,$request->empid,$request->fromdate,$request->todate);
        return $details;

    }
	

       
	public function manualAttendance(Request $request,attendance $attendance)
	{
		$branches = $attendance->getbranch();
		return view("Attendance.manual-mark-attendance",compact('branches'));
	}
	
	public function getDepartmentsFromBranch(Request $request)
	{
		if($request->branch){
			$departments = DB::table("departments")->where("branch_id",$request->branch)->get();
			return response()->json(["status" => 200,"departments" => $departments,"message" => "Department fetch completed"]);
		}else{
			return response()->json(["status" => 500,"message" => "Branch Id not found"]);
		}
	}
	
	public function getEmployeesFromdepartment(Request $request)
	{
		if($request->department_id){
			$employees = DB::table("employee_shift_details")->where("department_id",$request->department_id)->pluck("emp_id");
			$employees = DB::table("employee_details")
						->join("employee_shift_details","employee_shift_details.emp_id","=","employee_details.empid")
						->join("office_shift","office_shift.shift_id","=","employee_shift_details.shift_id")
						->whereIn("empid",$employees)->get();
			return response()->json(["status" => 200,"employees" => $employees,"message" => "Employee fetch completed"]);
		}else{
			return response()->json(["status" => 500,"message" => "Department Id not found"]);
		}
	}
	
	public function saveManualAttendance(Request $request)
	{
		if(!empty($request)){
			try{
				DB::beginTransaction();
				for($i = 0; $i< count($request->empid); $i++){
					// echo $request->empid[$i]."</br>";
					if($request->attendance[$i] == "present"){
						DB::table("attendance_details")->insert([
							"emp_id" => $request->empid[$i],
							"branch_id" => $request->branch,
							"date" => $request->fromdate,
							"clock_in" => $request->clock_in[$i],
							"clock_out" => $request->clock_out[$i],
							"late" =>0 ,
							"early" =>0 ,
							"OT_time" => 0,
							"ATT_time" => 0,
						]);
					}else if($request->attendance[$i] == "absent"){
						DB::table("absent_details")->insert([
							"acc_no" => $request->empid[$i],
							"absent_date" => $request->fromdate,
							"weekday" => 0,
							"event" => 0,
						]);
					}
				}
				DB::commit();
				return redirect("mark-manual-attendance");
			}catch(Exception $e){
				DB::rollback();
				return redirect("mark-manual-attendance");
			}
		}
	}
}
