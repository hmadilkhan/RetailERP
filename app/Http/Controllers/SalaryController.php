<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\salary;
use App\bank;
use App\loan;
use App\advanceSalary;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DateTime;
use PDF;
use Crabbly\Fpdf\Fpdf;
use App\pdfClass;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function branchwiseview(salary $salary, request $request)
    {
        $branch = $salary->getbranches();

        return view('Salary.branchwise-salary', compact('branch'));
    }

    public function departwisesalary(salary $salary, request $request)
    {
        $branch = $salary->getbranches();
        $depart = $salary->getdepart();
        return view('Salary.departwise-salary', compact('branch', 'depart'));
    }

    public function employeewiseview(salary $salary, request $request)
    {
        //  	$getemp  = $salary->getemployee(session("branch"));
        $branches = DB::table("branch")->where("company_id", session("company_id"))->get();
        $salcat = $salary->getsalcategory();
        return view('Salary.individual-salary', compact('salcat', 'branches'));
    }

    public function getEmployeesByBranch(salary $salary, Request $request)
    {
        if ($request->branch != "") {
            $employees = $salary->getemployee($request->branch);
            return response()->json(["status" => 200, "employees" => $employees]);
        } else {
            return response()->json(["status" => 500, "message" => "Branch Id is null"]);
        }
    }


    public function show(salary $salary, request $request)
    {
        $branch = $salary->getbranches();
        $emp = [];
        $details = $salary->salary_details('', '', "", "");
        return view('Salary.salary-details', compact('branch', 'emp', 'details'));
    }

    public function insert_specialallowance(salary $salary, request $request)
    {
        $exsists = $salary->exsit_chk_special($request->empid, $request->date);
        if ($exsists[0]->counter == 0) {
            $items = [
                'emp_id' => $request->empid,
                'amount' => $request->amount,
                'date' => $request->date,
                'reason' => $request->reason,
            ];
            $special = $salary->insert('special_allowance', $items);
            return 1;
        } else {
            $specialamt = $exsists[0]->amount;
            $specialamt = $specialamt + $request->amount;
            $items = [
                'amount' => $specialamt,
                'date' => $request->date,
                'reason' => $request->reason,
            ];
            $special = $salary->special_update($exsists[0]->special_id, $items);
            return $specialamt;
        }
    }

    public function getempdetails(salary $salary, request $request)
    {
        $emp = $salary->emp_details($request->empid, $request->fromdate, $request->todate);
        return $emp;
    }

    public function getallowance(salary $salary, request $request)
    {
        $last_day = date('Y-m-t', strtotime($request->fromdate));
        $first_day = date('Y-m-01', strtotime($request->fromdate));
        $totaldays = date('t', strtotime($request->fromdate));
        if ($last_day == $request->fromdate) {
            $presnet = $salary->present_record($first_day, $last_day, $request->empid);
            if ($presnet[0]->present == $totaldays) {
                return $presnet[0]->allowance;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function getgrossdetails(salary $salary, request $request)
    {

        $permission = $salary->check_permision();
        $salarydetails = DB::table("increment_details")->where("emp_id", $request->empid)->get();
        $employee = DB::table("employee_details")->where("empid", $request->empid)->get();
        $totalsalary = DB::table("increment_details")->where("emp_id", $request->empid)->get();
        $totalminutes = 14400; // This come from the calculation Total day * daily hours * total second = 30 * 8 * 60 = 14400
        $security_deposit = 0;
        $perminsalary = 0;
        $otamount = 0;

        if (!empty($totalsalary)) {
            if ($totalsalary[0]->gross_salary == "0" or $totalsalary[0]->gross_salary == "") {
                $perminsalary = number_format($totalsalary[0]->basic_pay / $totalminutes, 2);
            } else {
                $perminsalary = number_format($totalsalary[0]->gross_salary * $totalminutes, 2);
            }
        }

        if (count($permission) != 0) {
            if ($permission[0]->allowances != 0) {
                $allowances = $salary->getallowance_details($request->empid);
            } else {
                $allowances = "";
            }
            if ($permission[0]->bonus != 0) {
                $bonus = $salary->getbonus_details($request->empid, $request->fromdate, $request->todate);
            } else {
                $bonus = "";
            }
            if ($permission[0]->overtime != 0) {
                $overtime = $salary->get_overtimeAmount($request->empid, $request->fromdate, $request->todate);
            } else {
                $overtime = "";
            }
            $ot = $salary->calculateOvertime($request->empid, $request->fromdate, $request->todate);
            if ($ot[0]->ot > 0) {
                $otamount = $perminsalary * $ot[0]->ot;
            }

            return view('Salary.grosshead-salary', compact('allowances', 'bonus', 'overtime', 'salarydetails', 'employee', 'otamount'));
        } else {
            return 0;
        }
    }


    public function getdeduction(salary $salary, request $request)
    {
        $permission = $salary->check_permision();
        $eobi = DB::table("eobi")->where("company_id", session("company_id"))->get();
        $employee = DB::table("employee_details")->where("empid", $request->empid)->get();
        $totalsalary = DB::table("increment_details")->where("emp_id", $request->empid)->get();
        $totalminutes = 14400; // This come from the calculation Total day * daily hours * total second = 30 * 8 * 60 = 14400
        $security_deposit = 0;
        $perminsalary = 0;

        if (!empty($totalsalary)) {
            if ($totalsalary[0]->gross_salary == "0" or $totalsalary[0]->gross_salary == "") {
                $perminsalary = number_format($totalsalary[0]->basic_pay / $totalminutes, 2);
            } else {
                $perminsalary = number_format($totalsalary[0]->gross_salary * $totalminutes, 2);
            }
        }

        if (count($permission) != 0) {
            if ($permission[0]->advance != 0) {
                $advance = $salary->getadvance_details($request->empid, $request->fromdate, $request->todate);
            } else {
                $advance = "";
            }
            if ($permission[0]->loan != 0) {
                $loan = $salary->getloanamt_details($request->empid);
            } else {
                $loan = "";
            }
            if ($permission[0]->taxes != 0) {
                $tax = $salary->gettax_amount($request->empid);
            } else {
                $tax = "";
            }

            $absentamt = $salary->getabsent_amount($request->empid, $request->fromdate, $request->todate);
            $late = $salary->calculateLate($request->empid, $request->fromdate, $request->todate);
            $early = $salary->calculateEarly($request->empid, $request->fromdate, $request->todate);
            $ot = $salary->calculateOvertime($request->empid, $request->fromdate, $request->todate);

            $lateamount = 0;
            $earlyamount = 0;
            $otamount = 0;

            if ($late[0]->late > 0) {
                $lateamount = $perminsalary * $late[0]->late;
            }

            if ($early[0]->early > 0) {
                $earlyamount = $perminsalary * $early[0]->early;
            }

            if ($ot[0]->ot > 0) {
                $otamount = $perminsalary * $ot[0]->ot;
            }

            if ($employee[0]->security_deposit == 1) {
                $branch = DB::table("employee_shift_details")->where("emp_id", $request->empid)->get();
                $deposits = DB::table("hr_branch_security_deposit")->where("branch_id", $branch[0]->branch_id)->get();
                $totalamount = DB::select("SELECT SUM(credit)+SUM(debit) as balance FROM `employee_security_deposit_ledger` where emp_id = " . $request->empid);

                if ($totalamount[0]->balance < $deposits[0]->total_limit) {
                    $security_deposit = $deposits[0]->monthly_deduction;
                }
            }
            return view('Salary.deductionhead-salary', compact('advance', 'loan', 'absentamt', 'tax', 'eobi', 'employee', 'security_deposit', 'employee', 'lateamount', 'earlyamount', 'otamount'));
        } else {
            return 0;
        }
    }

    public function getspecial_allowances(salary $salary, request $request)
    {
        $special = $salary->getspecial_allowance($request->empid, $request->fromdate, $request->todate);
        return $special;
    }


    public function insert_payslip(salary $salary, request $request)
    {
        $loanamount = 0;

        if ($request->salcategory == 1) {
            $generatedates = $salary->generatedates($request->fromdate, $request->payslipdate);
            $permission = $salary->check_permision();
            foreach ($generatedates as $values) {
                $dayoff = $salary->getdayoff($request->empid);
                foreach ($dayoff as $weekend) {
                    if ($weekend->day_off != $values->Dayname) {
                        $exist = $salary->payslip_exsit($values->selected_date, $request->empid);
                        if ($exist[0]->counter == 0) {
                            $emp = $salary->emp_details($request->empid, $values->selected_date, $values->selected_date);
                            if ($permission[0]->allowances != 0) {
                                $allwanceamount = 0;
                                $allowances = $salary->getallowance_details($request->empid);
                                if (!empty($allowances)) {
                                    foreach ($allowances as $data) {
                                        $allwanceamount = $allwanceamount + $data->amount;
                                    }
                                } else {
                                    $allwanceamount = 0;
                                }
                            } else {
                                $allowances = "";
                            }
                            if ($permission[0]->bonus != 0) {
                                $bonus = $salary->getbonus_details($request->empid, $values->selected_date, $values->selected_date);
                            } else {
                                $bonus = "";
                            }
                            if ($permission[0]->overtime != 0) {
                                $overtime = $salary->get_overtimeAmount($request->empid, $values->selected_date, $values->selected_date);
                            } else {
                                $overtime = "";
                            }
                            if ($permission[0]->advance != 0) {
                                $advance = $salary->getadvance_details($request->empid, $values->selected_date, $values->selected_date);
                            } else {
                                $advance = "";
                            }
                            if ($permission[0]->loan != 0) {
                                $loan = $salary->getloanamt_details($request->empid);
                            } else {
                                $loan = "";
                            }
                            if ($permission[0]->taxes != 0) {
                                $tax = $salary->gettax_amount($request->empid);
                            } else {
                                $tax = "";
                            }
                            $absentamt = $salary->getabsent_amount($request->empid, $values->selected_date, $values->selected_date);
                            $special = $salary->getspecial_allowance($request->empid, $values->selected_date, $values->selected_date);
                            if ($request->payslipdate == $values->selected_date) {
                                $loanamount = $request->loanamt;
                            }
                            $gross = (float)((float)($overtime[0]->otamount == "" ? 0 : $overtime[0]->otamount) + (float)($allwanceamount == "" ? 0 : $allwanceamount) + (float)($bonus[0]->bonus_amount == "" ? 0 : $bonus[0]->bonus_amount) + (float)($emp[0]->basic_salary));
                            $deduction = (float)((float)($advance[0]->advance == "" ? 0 : $advance[0]->advance) + (float)($loanamount == "" ? 0 : $loanamount) + (float)($absentamt[0]->absent_amt == "" ? 0 : $absentamt[0]->absent_amt) + (float)($tax[0]->tax_amount == "" ? 0 : $tax[0]->tax_amount));
                            $net = (float)(($gross - $deduction) + (float)($special[0]->amount == "" ? 0 : $special[0]->amount));

                            $items = [
                                'emp_id' => $request->empid,
                                'present' => ($emp[0]->present == "" ? 0 : $emp[0]->present),
                                'absent' => ($emp[0]->absent == "" ? 0 : $emp[0]->absent),
                                'leaves' => ($emp[0]->leaves == "" ? 0 : $emp[0]->leaves),
                                'ot_hours' => ($emp[0]->ot == "" ? 0 : $emp[0]->ot),
                                'ot_amount' => ($overtime[0]->otamount == "" ? 0 : $overtime[0]->otamount),
                                'advance_amount' => ($advance[0]->advance == "" ? 0 : $advance[0]->advance),
                                'loan_amount' => ($loanamount == "" ? 0 : $loanamount),
                                'absent_amount' => ($absentamt[0]->absent_amt == "" ? 0 : $absentamt[0]->absent_amt),
                                'special_amount' => ($special[0]->amount == "" ? 0 : $special[0]->amount),
                                'tax_amount' => ($tax[0]->tax_amount == "" ? 0 : $tax[0]->tax_amount),
                                'bonus_amount' => ($bonus[0]->bonus_amount == "" ? 0 : $bonus[0]->bonus_amount),
                                'basic_salary' => $emp[0]->basic_salary,
                                'payslip_date' => $values->selected_date,
                                'allowance_amount' => ($allwanceamount == "" ? 0 : $allwanceamount),
                                'gross_salary' => $gross,
                                'deduction_amount' => $deduction,
                                'net_salary' => $net,
                            ];
                            $payslip = $salary->insert('payslip', $items);

                            $items = [
                                'emp_id' => $request->empid,
                                'date' => $values->selected_date,
                                'gross_salary' => $gross,
                                'deduction_salary' => $deduction,
                                'net_salary' => $net,
                                'special_amount' => ($special[0]->amount == "" ? 0 : $special[0]->amount),
                            ];
                            $salarydet = $salary->insert('salary_details', $items);

                            //update bonus details
                            if ($bonus[0]->bonus_id != "") {
                                $bonus = $salary->bonus_update($bonus[0]->bonus_id, 2);
                            }
                            //update advance details
                            if ($advance[0]->advance_id != "") {
                                $advance = $salary->advance_update($advance[0]->advance_id, 2);
                            }
                        } else {
                            return  $values->selected_date;
                        }
                    }
                }
            }
        } else {
            try {
                DB::beginTransaction();
                $items = [
                    'emp_id' => $request->empid,
                    'present' => ($request->present == "" ? 0 : $request->present),
                    'absent' => ($request->absent == "" ? 0 : $request->absent),
                    'leaves' => ($request->leaves == "" ? 0 : $request->leaves),
                    'ot_hours' => ($request->othours == "" ? 0 : $request->othours),
                    'ot_amount' => ($request->otamount == "" ? 0 : $request->otamount),
                    'advance_amount' => ($request->advanceamt == "" ? 0 : $request->advanceamt),
                    'loan_amount' => ($request->loanamt == "" ? 0 : $request->loanamt),
                    'absent_amount' => ($request->absentamt == "" ? 0 : $request->absentamt),
                    'special_amount' => ($request->specialamt == "" ? 0 : $request->specialamt),
                    'tax_amount' => ($request->taxamt == "" ? 0 : $request->taxamt),
                    'bonus_amount' => ($request->bonusamt == "" ? 0 : $request->bonusamt),
                    'basic_salary' => $request->basicepay,
                    'payslip_date' => $request->payslipdate,
                    'allowance_amount' => ($request->allowanceamt == "" ? 0 : $request->allowanceamt),
                    'gross_salary' => $request->gross,
                    'deduction_amount' => $request->deduct,
                    'eobi' => $request->eobiamt,
                    'security_deposit' => $request->security_deposit,
                    'net_salary' => $request->net,
                ];
                $payslip = $salary->insert('payslip', $items);

                $items = [
                    'emp_id' => $request->empid,
                    'date' => $request->payslipdate,
                    'basic_salary' => $request->basicepay,
                    'pf_fund' => $request->pffundamt,
                    'allowance' => $request->otherallowanceamt,
                    'gross_salary' => $request->gross,
                    'deduction_salary' => $request->deduct,
                    'net_salary' => $request->net,
                    'special_amount' => $request->specialamt,
                ];

                $salarydet = $salary->insert('salary_details', $items);

                $items = [
                    'emp_id' => $request->empid,
                    'debit' => 0,
                    'credit' => $request->security_deposit,
                    'created_at' => date("Y-m-d H:i:s"),
                ];

                $salarydet = $salary->insert('employee_security_deposit_ledger', $items);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
            }
        }

        //update bonus details
        //            if ($request->bonusid != "") {
        //              $bonus = $salary->bonus_update($request->bonusid,2);
        //            }
        //            //update advance details
        //            if ($request->advanceid != "") {
        //              $advance = $salary->advance_update($request->advanceid,2);
        //            }
        //loan installment updates
        //            if ($request->loanamt != "") {
        //            $loaninsta = $salary->getloan_installmentdetails($request->empid,$request->fromdate,$request->todate);
        //            foreach ($loaninsta as $value) {
        //      $updatloaninsta = $salary->loaninstallment_update($value->installment_id,2);
        //      //check count if 0 then update main table
        //          $loaninsta = $salary->getloan_installmentdetails($request->empid,$request->fromdate,$request->todate);
        //              if (count($loaninsta) == 0) {
        //                    $loanupdate = $salary->loandetails_update($value->loan_id,2);
        //                  }
        //              }
        //            }

        //new scenario insert loan installment and update loan balance
        //this is for installment
        if ($request->loanamt != '' && $request->loanamt != 0) {
            $loanamt = $request->loanamt;
            $loandetails = $salary->getloandetails($request->empid);
            for ($i = 0; $i < sizeof($loandetails); $i++) {
                $balance = $loandetails[$i]->balance;
                $checkbal = $balance - $loanamt;
                if ($checkbal > 0) {
                    $items = [
                        'balance' => $checkbal,
                    ];
                    $loanupdate = $salary->loandetails_update($loandetails[$i]->loan_id, $items);
                    //installment insert here
                    $items = [
                        'emp_id' => $request->empid,
                        'loan_id' => $loandetails[$i]->loan_id,
                        'installment_amount' => $loanamt,
                        'date' => $request->payslipdate,
                        'status_id' => 2,
                    ];
                    $insta = $salary->insert('loan_installment', $items);
                    break;
                } else if ($checkbal < 0) {
                    $items = [
                        'balance' => 0,
                        'status_id' => 2,
                    ];
                    $loanupdate = $salary->loandetails_update($loandetails[$i]->loan_id, $items);
                    $loanamt = ($checkbal * (-1));
                    //installment insert here
                    $items = [
                        'emp_id' => $request->empid,
                        'loan_id' => $loandetails[$i]->loan_id,
                        'installment_amount' => $balance,
                        'date' => $request->payslipdate,
                        'status_id' => 2,
                    ];
                    $insta = $salary->insert('loan_installment', $items);
                } else {
                    $items = [
                        'balance' => 0,
                        'status_id' => 2,
                    ];
                    $loanupdate = $salary->loandetails_update($loandetails[$i]->loan_id, $items);
                    //installment insert here
                    $items = [
                        'emp_id' => $request->empid,
                        'loan_id' => $loandetails[$i]->loan_id,
                        'installment_amount' => $loanamt,
                        'date' => $request->payslipdate,
                        'status_id' => 2,
                    ];
                    $insta = $salary->insert('loan_installment', $items);
                    break;
                }
            }
        }
        return 1;
    }

    public function getdetails(salary $salary, request $request)
    {
        $details = $salary->salary_details($request->fromdate, $request->todate, $request->branchid, $request->empid);

        return $details;
    }

    public function createpdf(salary $salary, request $request)
    {

        $company = $salary->getcompany();
        $payslip = $salary->payslip_report($request->empid, $request->fromdate, $request->todate);
        $details = $salary->payslip_details($request->empid, $request->todate);

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Image(asset('storage/images/company/' . $company[0]->logo), 10, -4, -100);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 35, $company[0]->name, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, -25, $company[0]->address, 0, 1, 'L');
        $pdf->Cell(0, 35, 'Karachi, Karachi City, Sindh', 0, 1, 'L');
        $pdf->Cell(0, -25, $company[0]->ptcl_contact, 0, 1, 'L');
        $pdf->Cell(0, 10, '', 0, 1, 'R');
        $pdf->Cell(190, 5, '', '', 1); //SPACE
        $pdf->Cell(190, 1, '', 'T', 1); //SPACE

        $pdf->setFillColor(0, 0, 0);
        $pdf->setTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'SALARY SHEET', 0, 1, 'C', 1); //Here is center title
        $pdf->Cell(190, 2, '', 'T', 1); //SPACE

        $pdf->setFillColor(255, 255, 255);
        $pdf->setTextColor(0, 0, 0);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(25, 10, "Pay Slip No:", '', 0, 'L');
        $pdf->Cell(20, 10, $payslip[0]->payslip_id, '', 0, 'L');
        $pdf->Cell(30, 10, "Payslip Date:", '', 0, 'L');
        $pdf->Cell(40, 10, date("d M Y", strtotime($payslip[0]->payslip_date)), '', 0, 'L');
        $pdf->Cell(30, 10, "Generate Date:", '', 0, 'L');
        $pdf->Cell(46, 10, date("d M Y", strtotime($payslip[0]->generate_date)) . " " . date("h:i a", strtotime($payslip[0]->generate_date)), '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->Cell(190, 1, '', 'T', 1); //SPACE


        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "ACC | Employee Name", '', 0, 'L');
        $pdf->Cell(70, 10, "Father Name", '', 0, 'L');
        $pdf->Cell(46, 10, "Mobile Number", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', 'B', 10);
        $emp = $salary->emp_details($request->empid, $request->fromdate, $request->todate);

        $pdf->Cell(70, 1, $emp[0]->emp_acc . " | " . $emp[0]->emp_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->emp_fname, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->emp_contact, '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "Branch", '', 0, 'L');
        $pdf->Cell(70, 10, "Department", '', 0, 'L');
        $pdf->Cell(46, 10, "Designation", '', 1, 'L');

        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 1, $emp[0]->branch_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->department_name, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->designation_name, '', 1, 'L');

        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->Cell(190, 4, '', 'B', 1); //SPACE
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(32, 10, "Present Days", '', 0, 'L');
        $pdf->Cell(32, 10, "Absent Days", '', 0, 'L');
        $pdf->Cell(32, 10, "Late Count", '', 0, 'L');
        $pdf->Cell(32, 10, "Early Count", '', 0, 'L');
        $pdf->Cell(32, 10, "OT Duration", '', 0, 'L');
        $pdf->Cell(30, 10, "", '', 1, 'L'); //"Basic Salary"
        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(32, 1, $emp[0]->present, '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->absent, '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->late . " mints", '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->early . " mints", '', 0, 'L');
        $pdf->Cell(32, 1, $emp[0]->ot . " mints", '', 0, 'L');
        $pdf->Cell(30, 1, "", '', 1, 'L'); //$emp[0]->basic_salary
        $pdf->Cell(190, 6, '', '', 1); //SPACE

        $pdf->Cell(190, 2, '', 'T', 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(55, 10, "Gross Head", '', 0, 'L');
        $pdf->Cell(40, 10, "Amount", '', 0, 'L');
        $pdf->Cell(55, 10, "Deduction Head", '', 0, 'L');
        $pdf->Cell(40, 10, "Amount", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(55, 9, 'Basic Salary', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($details[0]->basic_salary, 2), 0, 0, 'L', 1);
        $pdf->Cell(55, 9, 'Advance', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($payslip[0]->advance_amount, 2), 0, 1, 'L', 1);

        $pdf->Cell(55, 9, 'PF Fund', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($details[0]->pf_fund, 2), 0, 0, 'L', 1);
        $pdf->Cell(55, 9, 'Loan', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($payslip[0]->loan_amount, 2), 0, 1, 'L', 1);

        $pdf->Cell(55, 9, 'Allowance', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($details[0]->allowance, 2), 0, 0, 'L', 1);
        $pdf->Cell(55, 9, 'Absent', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($payslip[0]->absent_amount, 2), 0, 1, 'L', 1);

        // $pdf->setFillColor(230,230,230); 
        $pdf->Cell(55, 9, 'Bonus', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($payslip[0]->bonus_amount, 2), 0, 0, 'L', 1);
        $pdf->Cell(55, 9, 'Tax', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($payslip[0]->tax_amount, 2), 0, 1, 'L', 1);


        // $pdf->setFillColor(255,255,255); 
        $pdf->Cell(55, 9, 'Over Time', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($payslip[0]->ot_amount, 2), 0, 0, 'L', 1);
        $pdf->Cell(55, 9, 'EOBI', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format($payslip[0]->eobi, 2), 0, 1, 'L', 1);


        // $pdf->setFillColor(255,255,255); 
        $pdf->Cell(55, 9, '', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, '', 0, 0, 'L', 1);
        $pdf->Cell(55, 9, 'Security Deposit', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, ($payslip[0]->security_deposit != "" ? number_format($payslip[0]->security_deposit, 2) : "0.00"), 0, 1, 'L', 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->setFillColor(0, 0, 0);
        $pdf->setTextColor(255, 255, 255);
        $pdf->Cell(55, 9, 'Gross Amount', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format(($details[0]->basic_salary + $details[0]->pf_fund + $details[0]->allowance + $payslip[0]->bonus_amount + $payslip[0]->ot_amount), 2), 0, 0, 'L', 1);
        $pdf->Cell(55, 9, 'Deduction Amount', 0, 0, 'L', 1);
        $pdf->Cell(40, 9, number_format(($payslip[0]->advance_amount + $payslip[0]->loan_amount + $payslip[0]->absent_amount + $payslip[0]->tax_amount + $payslip[0]->eobi + $payslip[0]->security_deposit), 2), 0, 1, 'L', 1);

        $pdf->Cell(190, 7, '', '', 1); //SPACE

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->setFillColor(230, 230, 230);
        $pdf->setTextColor(0, 0, 0);
        $pdf->Cell(50, 7, "Gross Salary:", '', 0, 'L', 1);
        $pdf->Cell(40, 7, number_format($payslip[0]->gross_salary, 2), '', 1, 'L', 1);

        $pdf->Cell(50, 7, "Special Amount:", '', 0, 'L', 1);
        $pdf->Cell(40, 7, number_format($payslip[0]->special_amount, 2), '', 1, 'L', 1);

        $deduct = $payslip[0]->loan_amount + $payslip[0]->absent_amount + $payslip[0]->advance_amount;
        $pdf->Cell(50, 7, "Deduction:", '', 0, 'L', 1);
        $pdf->Cell(40, 7, number_format(round($payslip[0]->deduction_amount), 2), '', 1, 'L', 1);

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 125, 0);
        $pdf->Cell(50, 7, "Net Salary:", '', 0, 'L', 1);
        $pdf->Cell(40, 7, "Rs. " . number_format($payslip[0]->net_salary, 2) . " /=", '', 1, 'L', 1);

        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->Cell(190, 2, '', 'T', 1); //SPACE
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(185, 4, 'Computer generated report requires no signature', 0, 1, 'C');
        $pdf->Cell(190, 2, '', 'B', 1);
        $pdf->Output('test.pdf', 'I');
    }

    public function employeeLedgerPDF(salary $salary, request $request)
    {
        $company = $salary->company(session('company_id'));

        $name = $salary->getemployeename($request->empid);

        $ledger = $salary->getledgerdetails($request->empid);

        if (!empty($ledger)) {
            $pdf = new pdfClass();
            $pdf->AliasNbPages();
            $pdf->AddPage();


            //qr code generate here
            $qrcodetext = $name[0]->emp_acc . " | " . $name[0]->emp_name . " | " . $name[0]->emp_contact . " | Employee Id: " . $name[0]->empid;
            $qrimage = "qr" . $name[0]->emp_name . ".png";
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, public_path('assets/images/employees/qrcode/' . $qrimage));

            //first row
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(35, 0, '', 0, 0);
            $pdf->Cell(65, 0, "Company Name:", 0, 0, 'L');
            $pdf->Cell(45, 0, "Vendor Name", 0, 1, 'L');
            $pdf->Cell(30, 0, "", 0, 1, 'L');

            //second row
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(35, 0, '', 0, 0);
            $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 12, 10, -200);
            $pdf->Cell(65, 12, $company[0]->name, 0, 0, 'L');
            $pdf->Cell(45, 12, $name[0]->emp_name, 0, 0, 'L');
            $pdf->Cell(30, 0, "", 0, 1, 'R');
            $pdf->Image(public_path('assets/images/employees/qrcode/' . $qrimage), 175, 10, -200);

            //third row
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(35, 25, '', 0, 0);
            $pdf->Cell(65, 25, "Contact Number:", 0, 0, 'L');
            $pdf->Cell(45, 25, "Contact Number:", 0, 0, 'L');
            $pdf->Cell(30, 25, "", 0, 1, 'L');

            //forth row
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(35, -15, '', 0, 0);
            $pdf->Cell(65, -15, $company[0]->ptcl_contact, 0, 0, 'L');
            $pdf->Cell(45, -15, $name[0]->emp_contact, 0, 0, 'L');
            $pdf->Cell(30, -15, "", 0, 1, 'L');

            //fifth row
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(35, 28, '', 0, 0);
            $pdf->Cell(105, 28, "Company Address:", 0, 0, 'L');
            $pdf->Cell(50, 28, "", 0, 1, 'L');

            //sixth row
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(35, -18, '', 0, 0);
            $pdf->Cell(105, -18, $company[0]->address, 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

            //report name
            $pdf->ln(15);
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->Cell(190, 10, $name[0]->emp_name . ' | Ledger Report', 'B,T', 1, 'L');


            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(10, 8, 'Sr.', 'B', 0, 'L');
            $pdf->Cell(25, 8, 'Date', 'B', 0, 'L');
            $pdf->Cell(25, 8, 'Debit', 'B', 0, 'R');
            $pdf->Cell(25, 8, 'Credit', 'B', 0, 'R');
            $pdf->Cell(25, 8, 'Balance', 'B', 0, 'R');
            $pdf->Cell(80, 8, 'Narration', 'B', 1, 'L');

            $count = 0;
            foreach ($ledger as $key => $value) {
                $count++;
                if ($count % 2 == 0) {
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->setFillColor(232, 232, 232);
                    $pdf->SetTextColor(0, 0, 0);
                } else {
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->setFillColor(255, 255, 255);
                    $pdf->SetTextColor(0, 0, 0);
                }
                $pdf->Cell(10, 6, $key + 1, 0, 0, 'L', 1);
                $pdf->Cell(25, 6, date("d F Y", strtotime($value->date)), 0, 0, 'L', 1);
                $pdf->Cell(25, 6, number_format($value->debit, 2), 0, 0, 'R', 1);
                $pdf->Cell(25, 6, number_format($value->credit, 2), 0, 0, 'R', 1);
                $pdf->Cell(25, 6, number_format($value->balance, 2), 0, 0, 'R', 1);
                $pdf->Cell(80, 6, $value->narration, 0, 1, 'L', 1);
            }

            $pdf->ln();
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(130, 8, '', 0, 0, 'R');
            $pdf->Cell(20, 8, 'Total:', 'T,B', 0, 'R');
            //            $pdf->Cell(40,8,"Rs. ".number_format($totalBalance[0]->balance,2) ,'T,B',1,'R');





            $pdf->Output($name[0]->emp_name . ' Ledger.pdf', 'I');
        }
    }


    public  function  getemp_sal_category(salary $salary, Request $request)
    {
        $result = $salary->getemployee_bysalarycategory($request->branch, $request->catid);
        return $result;
    }

    public  function  getweekends(salary $salary, Request $request)
    {

        $newarr = array();
        $totalDays = $salary->totalmonthdays($request->fromdate, $request->todate);
        $dayoff = $salary->getdayoff($request->empid);

        foreach ($dayoff as $key => $value) {
            $newarr[$key] =  $value->day_off;
        }
        // return $newarr;
        $days = implode(',', $newarr);
        $result = $salary->getdaysname($request->fromdate, $request->todate, $days);
        return count($result);
    }


    public function insert_emp_ledger(salary $salary, bank $bank, Request $request)
    {

        $prebal = $salary->getpreviousbalance($request->empid);

        if ($request->mode == 1) {
            //agar balance minus me ae ga to

            if ($prebal[0]->balance <= 0) {
                return 0;
            }
            //agar behja gaya amount bara ho previous balance se to error
            else if ($prebal[0]->balance < $request->amount) {
                return 2;
            }
            //agar balance positive me ae ga to
            else {
                $bal = $prebal[0]->balance - $request->amount;
            }

            $items = [
                'emp_id' => $request->empid,
                'debit' => $request->amount,
                'credit' => 0,
                'balance' => $bal,
                'narration' => $request->narration,
            ];
            $ledger = $salary->insert('employee_ledger', $items);


            return $this->cashledger($bank, $request->amount, $request->narration);
        } else {
            //first time null ae ga
            if (empty($prebal)) {
                $bal = $request->amount;
            }
            //agar balance minus me ae ga to
            else if ($prebal[0]->balance <= 0) {
                $bal = $prebal[0]->balance - $request->amount;
            }
            //agar balance positive me ae ga to
            else {
                $bal = $prebal[0]->balance + $request->amount;
            }

            $items = [
                'emp_id' => $request->empid,
                'debit' => 0,
                'credit' => $request->amount,
                'balance' => $bal,
                'narration' => "Salary Deposit",
            ];
            $ledger = $salary->insert('employee_ledger', $items);
            return $this->cashledger($bank, $request->amount, $request->narration);
        }
    }

    public function show_emp_ledger(salary $salary, Request $request)
    {

        $ledger = $salary->getledger();
        return view('Salary.employeeLedger', compact('ledger'));
    }

    public function emp_ledgerdetails(salary $salary, Request $request)
    {
        $details = $salary->getledgerdetails($request->empid);
        return $details;
    }


    public function cashledger(bank $bank, $amount, $narration)
    {

        $balanceStock = $bank->getLastCashBalance();

        if (sizeof($balanceStock) > 0) {
            if ($balanceStock[0]->balance >= $amount) {
                $balance = $balanceStock[0]->balance -  $amount;
                $items = [
                    'branch_id' => session('branch'),
                    'date' => date("Y-m-d"),
                    'debit' => $amount,
                    'credit' => 0,
                    'balance' => $balance,
                    'narration' => "Employee Salary :  " . $narration,
                ];
                $result = $bank->insert_bankdetails('cash_ledger', $items);

                return 1;
            } else {
                return 3;
            }
        } else {
            return 3;
        }
    }

    public function advanceVoucher(salary $salary, advanceSalary $advsal, request $request)
    {

        $company = $salary->getcompany();
        $details = $advsal->getdetailsById($request->advance_id);

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 10, -4, -100);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 35, $company[0]->name, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, -25, $company[0]->address, 0, 1, 'L');
        $pdf->Cell(0, 35, 'Karachi, Karachi City, Sindh', 0, 1, 'L');
        $pdf->Cell(0, -25, $company[0]->ptcl_contact, 0, 1, 'L');
        $pdf->Cell(0, 10, '', 0, 1, 'R');
        $pdf->Cell(190, 5, '', '', 1); //SPACE
        $pdf->Cell(190, 1, '', 'T', 1); //SPACE

        $pdf->setFillColor(0, 0, 0);
        $pdf->setTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'ADVANCE SALARY', 0, 1, 'C', 1); //Here is center title
        $pdf->Cell(190, 1, '', 'B', 1); //SPACE

        $pdf->setFillColor(255, 255, 255);
        $pdf->setTextColor(0, 0, 0);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(25, 10, "Voucher No:", '', 0, 'L');
        $pdf->Cell(20, 10, 1, $details[0]->advance_id, 0, 'L');
        $pdf->Cell(30, 10, "Voucher Date:", '', 0, 'L');
        $pdf->Cell(40, 10, date("d M Y", strtotime($details[0]->date)), 0, 'L'); //date("d M Y",strtotime($payslip[0]->payslip_date))
        $pdf->Cell(30, 10, "Generate Date:", '', 0, 'L');
        $pdf->Cell(46, 10, date("d M Y", strtotime($details[0]->created_at)) . " " . date("h:i a", strtotime($details[0]->created_at)), '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->Cell(190, 1, '', 'T', 1); //SPACE

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "ACC | Employee Name", '', 0, 'L');
        $pdf->Cell(70, 10, "Father Name", '', 0, 'L');
        $pdf->Cell(46, 10, "Mobile Number", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', 'B', 10);
        $emp = $salary->emp_details($request->empid, $request->fromdate, $request->todate);

        $pdf->Cell(70, 1, $emp[0]->emp_acc . " | " . $emp[0]->emp_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->emp_fname, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->emp_contact, '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "Branch", '', 0, 'L');
        $pdf->Cell(70, 10, "Department", '', 0, 'L');
        $pdf->Cell(46, 10, "Designation", '', 1, 'L');

        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 1, $emp[0]->branch_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->department_name, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->designation_name, '', 1, 'L');

        $pdf->Cell(190, 5, '', '', 1); //SPACE


        $pdf->Cell(190, 2, '', 'T', 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(55, 10, "Amount", '', 0, 'L');
        $pdf->Cell(40, 10, "Date", '', 0, 'L');
        $pdf->Cell(95, 10, "Reason", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(55, 1, $details[0]->amount, '', 0, 'L');
        $pdf->Cell(40, 1, $details[0]->date, '', 0, 'L');
        $pdf->Cell(95, 1, $details[0]->reason, '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->Cell(190, 5, '', '', 1); //SPACE
        $pdf->Cell(190, 2, '', 'T', 1); //SPACE
        $pdf->Cell(190, 20, '', '', 1); //SPACE

        $pdf->Cell(55, 7, "", '', 0, 'L');
        $pdf->Cell(40, 7, "", '', 0, 'L');
        $pdf->Cell(55, 7, "Employee", '', 0, 'R');
        $pdf->Cell(40, 7, "", 'B', 1, 'L');

        $pdf->Output('Advance Salary.pdf', 'I');
    }

    public function loanVoucher(salary $salary, loan $loan, request $request)
    {

        $company = $salary->getcompany();
        $details = $loan->getdetailsById($request->loan_id);
        $installments = $loan->getinstallments($request->loan_id);

        $pdf = app('Fpdf');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Image(public_path('assets/images/company/' . $company[0]->logo), 10, -4, -100);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(10, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 35, $company[0]->name, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, -25, $company[0]->address, 0, 1, 'L');
        $pdf->Cell(0, 35, 'Karachi, Karachi City, Sindh', 0, 1, 'L');
        $pdf->Cell(0, -25, $company[0]->ptcl_contact, 0, 1, 'L');
        $pdf->Cell(0, 10, '', 0, 1, 'R');
        $pdf->Cell(190, 5, '', '', 1); //SPACE
        $pdf->Cell(190, 1, '', 'T', 1); //SPACE

        $pdf->setFillColor(0, 0, 0);
        $pdf->setTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'LOAN VOUCHER', 0, 1, 'C', 1); //Here is center title
        $pdf->Cell(190, 1, '', 'B', 1); //SPACE

        $pdf->setFillColor(255, 255, 255);
        $pdf->setTextColor(0, 0, 0);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(25, 10, "Voucher No:", '', 0, 'L');
        $pdf->Cell(20, 10, 1, $details[0]->loan_id, 0, 'L');
        $pdf->Cell(30, 10, "Voucher Date:", '', 0, 'L');
        $pdf->Cell(40, 10, date("d M Y", strtotime($details[0]->date)), 0, 'L'); //date("d M Y",strtotime($payslip[0]->payslip_date))
        $pdf->Cell(30, 10, "Generate Date:", '', 0, 'L');
        $pdf->Cell(46, 10, date("d M Y", strtotime($details[0]->created_at)) . " " . date("h:i a", strtotime($details[0]->created_at)), '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->Cell(190, 1, '', 'T', 1); //SPACE

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "ACC | Employee Name", '', 0, 'L');
        $pdf->Cell(70, 10, "Father Name", '', 0, 'L');
        $pdf->Cell(46, 10, "Mobile Number", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', 'B', 10);
        $emp = $salary->emp_details($request->empid, $request->fromdate, $request->todate);

        $pdf->Cell(70, 1, $emp[0]->emp_acc . " | " . $emp[0]->emp_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->emp_fname, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->emp_contact, '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE


        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(70, 10, "Branch", '', 0, 'L');
        $pdf->Cell(70, 10, "Department", '', 0, 'L');
        $pdf->Cell(46, 10, "Designation", '', 1, 'L');

        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 1, $emp[0]->branch_name, '', 0, 'L');
        $pdf->Cell(70, 1, $emp[0]->department_name, '', 0, 'L');
        $pdf->Cell(46, 1, $emp[0]->designation_name, '', 1, 'L');

        $pdf->Cell(190, 5, '', '', 1); //SPACE


        $pdf->Cell(190, 2, '', 'T', 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(55, 10, "Amount", '', 0, 'L');
        $pdf->Cell(40, 10, "Date", '', 0, 'L');
        $pdf->Cell(95, 10, "Reason", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE

        $pdf->SetFont('Arial', '', 10);
        $pdf->setFillColor(230, 230, 230);
        $pdf->Cell(55, 1, $details[0]->loan_amount, '', 0, 'L');
        $pdf->Cell(40, 1, $details[0]->date, '', 0, 'L');
        $pdf->Cell(95, 1, $details[0]->reason, '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE



        $pdf->Cell(190, 5, '', '', 1); //SPACE
        $pdf->Cell(190, 1, '', 'T', 1); //SPACE


        $pdf->setFillColor(0, 0, 0);
        $pdf->setTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'LOAN INSTALLMENT', 0, 1, 'C', 1); //Here is center title
        $pdf->Cell(190, 1, '', 'B', 1); //SPACE

        $pdf->setFillColor(255, 255, 255);
        $pdf->setTextColor(0, 0, 0);

        $pdf->Cell(190, 2, '', 'T', 1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(55, 10, "Date", '', 0, 'L');
        $pdf->Cell(40, 10, "Amount", '', 1, 'L');
        $pdf->Cell(190, 1, '', '', 1); //SPACE
        $pdf->SetFont('Arial', '', 12);
        foreach ($installments as $installment) {
            $pdf->Cell(55, 10, $installment->date, '', 0, 'L');
            $pdf->Cell(40, 10, $installment->installment_amount, '', 1, 'L');
            $pdf->Cell(190, 1, '', '', 1); //SPACE
        }


        $pdf->Cell(190, 20, '', '', 1); //SPACE
        $pdf->Cell(55, 7, "", '', 0, 'L');
        $pdf->Cell(40, 7, "", '', 0, 'L');
        $pdf->Cell(55, 7, "Employee", '', 0, 'R');
        $pdf->Cell(40, 7, "", 'B', 1, 'L');

        $pdf->Output('Advance Salary.pdf', 'I');
    }
}
