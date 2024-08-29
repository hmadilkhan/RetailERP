<?php

use App\Http\Controllers\AdvanceSalaryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BankDiscountController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\BusinessPoliciesController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FloorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HRPermissionController;
use App\Http\Controllers\IncrementController;
use App\Http\Controllers\KitchenDepartmentController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryController;

/******************************************** HR ROUTES *************************************************************/
//HR Permission
Route::get('/showhrpermission', [HRPermissionController::class, 'show']);
Route::POST('/store-hrpermission', [HRPermissionController::class, 'store']);

//Tax Slabs
Route::get('/showtaxslabs-active', [BusinessPoliciesController::class, 'show_taxslabs']);
Route::get('/showtaxslabs-create', [BusinessPoliciesController::class, 'createtaxslabs']);
Route::post('/store-taxslabs', [BusinessPoliciesController::class, 'store_taxslabs']);
Route::get('/showtaxslabs-inactive', [BusinessPoliciesController::class, 'show_taxslabsinactive']);
Route::put('/inactive-taxslab', [BusinessPoliciesController::class, 'inactivetaxslab']);
Route::put('/reactive-taxslab', [BusinessPoliciesController::class, 'reactivetaxslab']);
Route::post('/update-taxslabs', [BusinessPoliciesController::class, 'update_taxslabs']);

//Departments
Route::get('/view-departments', [DepartmentController::class, 'view']);
Route::get('/show-departments', [DepartmentController::class, 'show']);
Route::post('/insert-departments', [DepartmentController::class, 'store']);
Route::get('/edit-departments-show/{id}', [DepartmentController::class, 'edit']);
Route::post('/edit-departments', [DepartmentController::class, 'update']);
Route::put('/remove-departments', [DepartmentController::class, 'remove']);

//Designation
Route::get('/view-designation', [DesignationController::class, 'view']);
Route::get('/show-designation', [DesignationController::class, 'show']);
Route::post('/insert-designation', [DesignationController::class, 'store']);
Route::put('/remove-designation', [DesignationController::class, 'remove']);
Route::get('/edit-designation-show/{id}', [DesignationController::class, 'edit']);
Route::post('/edit-designation', [DesignationController::class, 'update']);

//Office Shift
Route::get('/view-shift', [AttendanceController::class, 'shiftview']);
Route::get('/show-shift', [AttendanceController::class, 'shiftshow']);
Route::post('/insert-shift', [AttendanceController::class, 'shiftinsert']);
Route::post('/delete-shift', [AttendanceController::class, 'deleteshift']);
Route::get('/show-editshift/{id}', [AttendanceController::class, 'shiftedit']);
Route::post('/update-shift', [AttendanceController::class, 'shiftupdate']);

//Over Time Formula
Route::get('/view-ot', [AttendanceController::class, 'OTtview']);
Route::get('/show-ot', [AttendanceController::class, 'OTshow']);
Route::post('/insert-ot', [AttendanceController::class, 'otinsert']);
Route::get('/show-editot/{id}', [AttendanceController::class, 'otedit']);
Route::post('/update-ot', [AttendanceController::class, 'otupdate']);
Route::post('/delete-ot', [AttendanceController::class, 'deleteot']);
Route::post('/insert-otamount', [AttendanceController::class, 'otamountinsert']);
Route::post('/insert-otduration', [AttendanceController::class, 'otdurationinsert']);


//Attendance
Route::get('/dailyattendance-view', [AttendanceController::class, 'attendanceview']);
Route::get('/dailyattendance-edit', [AttendanceController::class, 'attendanceedit']);
Route::get('/getemployees', [AttendanceController::class, 'getemployees']);
Route::get('/getattendancedetails', [AttendanceController::class, 'getattendetails']);
Route::get('/getgracetime', [AttendanceController::class, 'getgracetime']);
Route::post('/dailyattendance-update', [AttendanceController::class, 'attendanceupdate']);
Route::get('/getdatabybranchid', [AttendanceController::class, 'getdata']);
Route::post('/attendanceupload', [AttendanceController::class, 'uploadattendance']);
Route::get('/getsheet', [AttendanceController::class, 'getsheet']);
Route::get('/manuallyattendance', [AttendanceController::class, 'show']);
Route::post('/submitattendance', [AttendanceController::class, 'store']);
Route::get('/getpdfattendancesheet', [AttendanceController::class, 'attendancesheet_pdf']);
Route::get('/attnotify', [AttendanceController::class, 'attendance_notify']);
Route::post('/updatenotifyatt', [AttendanceController::class, 'update_att_notify']);
Route::get('/attnotify_chkout', [AttendanceController::class, 'att_notify_chkout']);
Route::post('/updatenotifyattclckout', [AttendanceController::class, 'update_att_notify_clckout']);
Route::post('/absent_delete', [AttendanceController::class, 'deleteabsent']);
Route::post('/absent_insert', [AttendanceController::class, 'insertabsent']);
Route::get('/absent_details', [AttendanceController::class, 'absentdetails']);
Route::get('/absent_details_filter', [AttendanceController::class, 'absentfilter']);
Route::get('/mark-manual-attendance', [AttendanceController::class, 'manualAttendance']);
Route::post('/mark-manual-attendance', [AttendanceController::class, 'saveManualAttendance']);
Route::post('/get-departments-from-branch', [AttendanceController::class, 'getDepartmentsFromBranch']);
Route::post('/get-employees-from-departments', [AttendanceController::class, 'getEmployeesFromdepartment']);

//Salary
Route::get('/branchwise-view', [SalaryController::class, 'branchwiseview']);
Route::get('/departwise-view', [SalaryController::class, 'departwisesalary']);
Route::get('/empwise-view', [SalaryController::class, 'employeewiseview']);
Route::get('/getempdetails', [SalaryController::class, 'getempdetails']);
Route::get('/getgross', [SalaryController::class, 'getgrossdetails']);
Route::post('/insert-specialallowance', [SalaryController::class, 'insert_specialallowance']);
Route::post('/insert-payslip', [SalaryController::class, 'insert_payslip']);
Route::get('/salary-details', [SalaryController::class, 'show']);
Route::get('/getsalarydetails', [SalaryController::class, 'getdetails']);
Route::get('/getallowance', [SalaryController::class, 'getallowance']);
Route::get('/getpdf', [SalaryController::class, 'createpdf']);
Route::get('/advance-salary', [SalaryController::class, 'advanceVoucher']);
Route::get('/loan-voucher', [SalaryController::class, 'loanVoucher']);
Route::get('/getdeduction', [SalaryController::class, 'getdeduction']);
Route::get('/getsepcialallowance', [SalaryController::class, 'getspecial_allowances']);
Route::get('/getemp_sal_category', [SalaryController::class, 'getemp_sal_category']);
Route::get('/getweekends', [SalaryController::class, 'getweekends']);
Route::post('/insert-emp-ledger', [SalaryController::class, 'insert_emp_ledger']);
Route::get('/show-emp-ledger', [SalaryController::class, 'show_emp_ledger']);
Route::get('/get-emp-ledgerdetails', [SalaryController::class, 'emp_ledgerdetails']);
Route::get('/getledgerpdf', [SalaryController::class, 'employeeLedgerPDF']);
Route::post('/get-employees-by-branch', [SalaryController::class, 'getEmployeesByBranch']);

//FLOORS
Route::get('/view-floors', [FloorController::class, 'index']);
Route::post('/create-floors', [FloorController::class, 'store']);
Route::post('/update-floors', [FloorController::class, 'update']);
Route::post('/delete-floors', [FloorController::class, 'deleteFloor']);

//BANK DISCOUNT
Route::get('/view-bank-discount', [BankDiscountController::class, 'index']);
Route::post('/create-bank-discount', [BankDiscountController::class, 'store']);
Route::post('/update-bank-discount', [BankDiscountController::class, 'update']);
Route::post('/delete-bank-discount', [BankDiscountController::class, 'deleteDiscount']);

//KITCHEN DEPARTMENT
Route::get('/view-kitchen-departments', [KitchenDepartmentController::class, 'index']);
Route::post('/save-kitchen-department', [KitchenDepartmentController::class, 'store']);
Route::get('/printers-kitchen-departments/{id}', [KitchenDepartmentController::class, 'printers']);
Route::post('/store-printing-details', [KitchenDepartmentController::class, 'storePrinters']);
Route::put('/update-depart', [KitchenDepartmentController::class, 'updatedepart']);
Route::post('/getsubkitchendepart', [KitchenDepartmentController::class, 'getKitchenDepart']);
Route::post('/update-kitchen-details-update', [KitchenDepartmentController::class, 'updateKitchenSubDepartment']);

//Employee
Route::get('/view-employee', [EmployeeController::class, 'view']);
Route::get('/show-employee', [EmployeeController::class, 'show']);
Route::post('/insert-employee', [EmployeeController::class, 'store']);
Route::get('/chk-employee', [EmployeeController::class, 'empacccheck']);
Route::post('/store-desg', [EmployeeController::class, 'store_desg']);
Route::post('/store-depart', [EmployeeController::class, 'store_depart']);
Route::post('/remove-employee', [EmployeeController::class, 'remove']);
Route::get('/view-inaciveemployee', [EmployeeController::class, 'viewinactive']);
Route::get('/details-employee/{id}', [EmployeeController::class, 'empdetails']);
Route::get('/edit-employee-show/{id}', [EmployeeController::class, 'edit']);
Route::post('/update-employee', [EmployeeController::class, 'update']);
Route::get('/switch-branch', [EmployeeController::class, 'switchbranch']);
Route::put('/emp-branch-change', [EmployeeController::class, 'branchupdate']);
Route::get('/fire-emp-show', [EmployeeController::class, 'fireshow']);
Route::get('/getshifts', [EmployeeController::class, 'getshifts']);
Route::post('/hire-employee', [EmployeeController::class, 'hireagain']);
Route::post('/insert-category', [EmployeeController::class, 'storecat']);
Route::get('/showholiday', [EmployeeController::class, 'show_holiday']);
// Route::get('/viewholiday',[EmployeeController::class,view_holiday']);
Route::get('/getempmonthly', [EmployeeController::class, 'getempmonthly']);
Route::post('/insert-holiday', [EmployeeController::class, 'storeholiday']);
Route::post('/update-holiday', [EmployeeController::class, 'updateholiday']);
Route::get('/showevent', [EmployeeController::class, 'show_event']);
Route::post('/insert-events', [EmployeeController::class, 'storeevents']);
Route::post('/update-events', [EmployeeController::class, 'updateevents']);
Route::put('/delete-events', [EmployeeController::class, 'deleteevents']);
Route::get('/getdepart-branchwise', [EmployeeController::class, 'getdeparts']);
Route::get('/getdesg-departwise', [EmployeeController::class, 'getdesig']);


//Qualification
Route::get('/getqualification', [EmployeeController::class, 'showqual']);
Route::post('/storeeducation', [EmployeeController::class, 'storeeducation']);
Route::get('/getqualification-details', [EmployeeController::class, 'getqual']);
Route::put('/deleteeducation', [EmployeeController::class, 'deletequal']);
Route::post('/updateeducation', [EmployeeController::class, 'updatequal']);

//Allowances
Route::get('/getallowances', [EmployeeController::class, 'showallowances']);
Route::post('/storeallowance', [EmployeeController::class, 'storeallowance']);
Route::post('/storeallowancedetails', [EmployeeController::class, 'storeallowancedetails']);
Route::get('/getallowancesdetails', [EmployeeController::class, 'allowancedetails']);
Route::put('/deleteallowance', [EmployeeController::class, 'deleteallowancedetails']);
Route::put('/updateallowance', [EmployeeController::class, 'updateallowancedetails']);

//Leaves
Route::get('/getleaves', [EmployeeController::class, 'showleaves']);
Route::post('/storeleavehead', [EmployeeController::class, 'storeleavehead']);
Route::post('/insert-leavedetails', [EmployeeController::class, 'store_leavedetails']);
Route::get('/getleavesdetails', [EmployeeController::class, 'leavesdetails']);
Route::put('/deleteleavesdetails', [EmployeeController::class, 'deleteleavesdetails']);
Route::put('/updateleavedetails', [EmployeeController::class, 'updateleavedetails']);

//Leaves Form
Route::get('/showleaves', [LeaveController::class, 'view']);
Route::get('/showleave_form', [LeaveController::class, 'showform']);
Route::get('/getleavehead', [LeaveController::class, 'leaveheads']);
Route::get('/getleavebalance', [LeaveController::class, 'leavebalance']);
Route::POST('/submitleave', [LeaveController::class, 'storeleaveform']);
Route::PUT('/updateleavestatus', [LeaveController::class, 'updatestatus']);


//Public Holiday
Route::get('/get-public-holidays', [EmployeeController::class, 'showPublicHolidays']);
Route::get('/create-public-holidays', [EmployeeController::class, 'createPublicHolidays']);
Route::post('/get-department-by-branch', [EmployeeController::class, 'getDepartments']);
Route::post('/mark-public-holiday', [EmployeeController::class, 'savePublicHoliday']);

//Increment Details
Route::get('/showincrement', [IncrementController::class, 'view']);
Route::get('/createincrement', [IncrementController::class, 'show']);
Route::get('/getbasicsalary', [IncrementController::class, 'getbasicsal']);
Route::get('/gettaxslab_byempid', [IncrementController::class, 'gettaxslab']);
Route::get('/getallowance_byempid', [IncrementController::class, 'getallowances']);
Route::post('/allowance_increment', [IncrementController::class, 'allowanceincre']);
Route::post('/store_increment', [IncrementController::class, 'store']);


//Bonus Details
Route::get('/showbonus', [BonusController::class, 'view']);
Route::get('/createbonus', [BonusController::class, 'show']);
Route::post('/store_bonus', [BonusController::class, 'store']);
Route::put('/delete_bonus', [BonusController::class, 'delete']);
Route::get('/editbonus/{id}', [BonusController::class, 'edit']);
Route::put('/update_bonus', [BonusController::class, 'update']);

//Promotion Details
Route::get('/showpromotion', [PromotionController::class, 'view']);
Route::get('/createpromotion', [PromotionController::class, 'show']);
Route::get('/getoldetails', [PromotionController::class, 'getoldetails']);
Route::get('/getdesigbyempid', [PromotionController::class, 'getdesigbyempid']);
Route::put('/promotion', [PromotionController::class, 'promote_employee']);


//Loan
Route::get('/view-loandeduct', [LoanController::class, 'view']);
Route::get('/show-loandeduct', [LoanController::class, 'show']);
Route::post('/insert-loandeduct', [LoanController::class, 'store']);
Route::post('/delete-loandeduct', [LoanController::class, 'deletededuct']);
Route::get('/edit-loandeduct/{id}', [LoanController::class, 'edit']);
Route::post('/update-loandeduct', [LoanController::class, 'updatededuct']);
Route::get('/loandetails', [LoanController::class, 'viewdetails']);
Route::get('/show-issueloan', [LoanController::class, 'showloan']);
Route::post('/get-employee', [LoanController::class, 'getempbybranch']);
Route::post('/issueloan', [LoanController::class, 'issueloan']);
Route::post('/insert-loandeduct-modal', [LoanController::class, 'insert']);
Route::post('/previousdata', [LoanController::class, 'getpreivousdetails']);
Route::put('/remove-loan', [LoanController::class, 'remove']);
Route::get('/getinstallments', [LoanController::class, 'getinstallments']);
Route::put('/loandeduction', [LoanController::class, 'loandeduction']);
Route::get('/getdetails_loan', [LoanController::class, 'getdetails_loan_inactive']);

//Advance Salary
Route::get('/view-advancelist', [AdvanceSalaryController::class, 'view']);
Route::get('/show-advancesal', [AdvanceSalaryController::class, 'show']);
Route::post('/get-employeebybranch', [AdvanceSalaryController::class, 'getempbybranch']);
Route::post('/insert-advance', [AdvanceSalaryController::class, 'store']);
Route::get('/previousdetails', [AdvanceSalaryController::class, 'getpreivousdetails']);
Route::get('/getinactivedetails', [AdvanceSalaryController::class, 'getinactivedetails']);
Route::get('/getbasicpay', [AdvanceSalaryController::class, 'getbasicsalary']);

//Reports
Route::get('/attrpt-show', [ReportController::class, 'attreport_show']);
Route::get('/attendancerpt', [ReportController::class, 'attendancereport']);
Route::get('/pdfattendance', [ReportController::class, 'pdf_attendance']);
Route::get('/pdfsalarysheet', [ReportController::class, 'consolidated_salary_sheet']);
Route::get('/pdfloandetails', [ReportController::class, 'pdf_loandetails']);
Route::get('/pdfadvancedetails', [ReportController::class, 'pdf_advancedetails']);
