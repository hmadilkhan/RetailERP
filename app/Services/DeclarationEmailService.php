<?php

namespace App\Services;

use App\dashboard;
use App\expense;
use App\Mail\DeclarationEmail;
use App\Models\Branch;
use App\Models\Company;
use App\Models\SalesOpening;
use App\report;
use App\userDetails;
use Crabbly\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DeclarationEmailService
{
    /**
     * Generate a complete report for email.
     *
     * @param int $companyId
     * @param int $branchId
     * @param int $terminalId
     * @param int $openingId
     * @return void
     */
    public function generateCompleteReportAndSendEmail($openingId)
    {
        // Process and prepare the report for email
        $this->prepareEmail($openingId);
    }

    /**
     * Fetch the necessary data for the report.
     *
     * @param int $companyId
     * @param int $branchId
     * @param int $terminalId
     * @param int $openingId
     * @return array
     */
    private function generatePdfReport($companyId, $branchId, $terminalId, $openingId)
    {
        $users = new userDetails();
        $dash = new dashboard();
        $report = new report();
        $company = Company::findOrFail($companyId);
        $branch = Branch::findOrFail($branchId);
        $permissions = $users->getPermission($terminalId);
        $terminal_name = $users->getTerminalName($terminalId);
        $heads = $dash->getheadsDetailsFromOpeningIdForClosing($openingId);
        $CashInHand = "";
        $declarationNo =  $heads[0]->opening_id  ?? 0;

        $positive =
            ($heads[0]->bal ?? 0) +
            ($heads[0]->order_delivered_cash ?? 0) +
            ($heads[0]->Cash ?? 0) +
            ($heads[0]->adv_booking_cash ?? 0) +
            ($heads[0]->cashIn ?? 0);
        $negative =
            ($heads[0]->Discount ?? 0) +
            ($heads[0]->SalesReturn ?? 0) +
            ($heads[0]->VoidReceipts ?? 0) +
            ($heads[0]->cashOut ?? 0);
        $CashInHand =
            $positive -
            $negative +
            ($heads[0]->CardCustomerDiscount ?? 0) +
            ($heads[0]->Delivery ?? 0);
        if (isset($permissions[0]->expenses) && $permissions[0]->expenses == 1) {
            $CashInHand -= $heads[0]->expenses;
        }
        if (session('company_id') == 102) {
            $CashInHand -= $heads[0]->bal ?? 0;
        }
        $CashInHand = round($CashInHand);
        $closingBalance = round($heads[0]->closingBal ?? 0);

        $pdf = new Fpdf('P', 'mm', array(80, 200));


        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetMargins(1, 0, 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTitle("Declaration Details");

        $pdf->Image('https://retail.sabsoft.com.pk/storage/images/company/' . $company->logo, 28, 4, -200);

        $pdf->ln(23);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 0, $company->name . " (" . $branch->branch_name . ") ", 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->ln(4);
        $pdf->Cell(80, 0, $terminal_name[0]->terminal_name, 0, 1, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->Multicell(80, 7, $branch->branch_address, 0, 'C', 0);
        $pdf->Cell(80, 1, $branch->branch_ptcl . " | " . $branch->branch_mobile, 0, 1, 'C');
        $pdf->ln(1);


        $pdf->ln(2);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(75, 6, 'SALES DECLARATION', 0, 0, 'C', 1);
        $pdf->ln(6);

        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        // HEAD
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(35, 4, "Opening DateTime  ", 0, 0, 'L');
        // CENTER SPACE
        $pdf->Cell(5, 4, ":", 0, 0, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(35, 4, date('d M Y', strtotime($heads[0]->date)) . ' ' . date('H:i a', strtotime($heads[0]->time)) ?? 0, 0, 1, 'R');

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(35, 4, "Closing DateTime  ", 0, 0, 'L');
        // CENTER SPACE
        $pdf->Cell(5, 4, ":", 0, 0, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(35, 4, date('d M Y', strtotime($heads[0]->closingDate)) . ' ' . date('H:i a', strtotime($heads[0]->closingTime)) ?? 0, 0, 1, 'R');

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15, 4, "Branch  ", 'T', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(20, 4, $terminal_name[0]->branch_name, 'T', 0, 'R');
        // CENTER SPACE
        $pdf->Cell(5, 4, "", 'T', 0, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(15, 4, "Terminal  ", 'T', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(20, 4, $terminal_name[0]->terminal_name, 'T', 1, 'R');

        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(20, 4, "Declaration No.", 'B', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 4, $declarationNo, 'B', 0, 'R');
        // CENTER SPACE
        $pdf->Cell(5, 4, "", 'B', 0, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(20, 4, "Closing Balance  ", 'B', 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 4,  $closingBalance, 'B', 1, 'R');

        $pdf->ln(2);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(75, 6, 'TRANSACTION DETAILS', 0, 0, 'C', 1);
        $pdf->ln(6);

        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        if ($permissions[0]->ob == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Opening Balance", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->bal, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->cash_sale == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Cash Sale", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->Cash, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->customer_credit_sale == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Credit Card Sale", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->CreditCard, 0) ?? 0, 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(38, 6, "Total Sales", 0, 0, 'L', 1);
        $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
        $pdf->Cell(30, 6, number_format($heads[0]->TotalSales + $heads[0]->credit_card_transaction, 0) ?? 0, 0, 1, 'R', 1);

        if ($permissions[0]->order_booking == 1) {

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Adv Booking (Cash)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->adv_booking_cash, 0) ?? 0, 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Adv Booking (Card)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->adv_booking_card, 0) ?? 0, 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Order Delivered (Cash)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->order_delivered_cash, 0) ?? 0, 0, 1, 'R', 1);

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Order Delivered (Card)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->order_delivered_card, 0) ?? 0, 0, 1, 'R', 1);
        }

        if ($permissions[0]->sale_return == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Sale Return", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->SalesReturn, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->void_receipt == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Void Receipts", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->VoidReceipts, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->discount == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Discount", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->Discount, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->cash_in == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Cash In", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->cashIn, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->cash_out == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Cash Out", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->cashOut, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->expenses == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Expense", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->expenses, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->fbr_sync == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "FBR (TAX)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->fbr, 0) ?? 0, 0, 1, 'R', 1);
        }
        if ($permissions[0]->srb_sync == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "SRB (TAX)", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($heads[0]->srb, 0) ?? 0, 0, 1, 'R', 1);
        }

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(38, 6, "Cash In Hand", 0, 0, 'L', 1);
        $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
        $pdf->Cell(30, 6, number_format($CashInHand, 0) ?? 0, 0, 1, 'R', 1);


        if ($permissions[0]->cb == 1) {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(38, 6, "Closing Balance", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, number_format($closingBalance, 0) ?? 0, 0, 1, 'R', 1);
        }

        $status = "";
        if ($closingBalance > $CashInHand) {
            $pdf->SetTextColor(255, 0, 0);
            $status = '(' . ($closingBalance - $CashInHand) . ' Amount Excess)';
        } else if ($closingBalance < $CashInHand) {
            $pdf->SetTextColor(255, 0, 0);
            $status =  '(' . ($closingBalance - $CashInHand) . ' Amount Short)';
        } else if ($closingBalance == $CashInHand) {
            $pdf->SetTextColor(34, 139, 34);
        }

        if ($permissions[0]->cb == 1) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(38, 6, "", 0, 0, 'L', 1);
            $pdf->Cell(7, 6, ":", 0, 0, 'C', 1);
            $pdf->Cell(30, 6, $status, 0, 1, 'R', 1);
        }


        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->ln(6);

        $pdf->AddPage();

        $pdf->ln(6);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(78, 6, 'ITEM SALES DETAILS', 0, 0, 'C', 1);
        $pdf->ln(6);

        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->setFillColor(233, 233, 233);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(40, 7, 'Product', 0, 0, 'L', 1);
        $pdf->Cell(13, 7, 'Price', 0, 0, 'L', 1);
        $pdf->Cell(11, 7, 'Qty', 0, 0, 'C', 1);
        $pdf->Cell(14, 7, 'Amount', 0, 1, 'C', 1);
        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        $items = $report->itemsalesdatabaseforpdf($openingId);

        $totalPrice = 0;
        $totalQty = 0;
        $totalWeightQty = 0;
        $totalAmount = 0;
        foreach ($items as $key => $item) {
            $totalPrice += $item->price;
            $totalQty += $item->qty;
            $totalWeightQty += ($item->qty * $item->weight_qty);
            $totalAmount += $item->amount;

            $pdf->Cell(78, 7, "(" . $item->item_code . ") " . $item->product_name, 0, 1, 'L', 1);
            $pdf->Cell(40, 7, number_format($item->price, 0), 0, 0, 'L', 1);
            $pdf->Cell(13, 7, $item->qty, 0, 0, 'L', 1);
            $pdf->Cell(11, 7, $item->qty * $item->weight_qty, 0, 0, 'C', 1);
            $pdf->Cell(14, 7, number_format($item->amount, 0), 0, 1, 'C', 1);
        }

        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(40, 7, number_format($totalPrice, 0), 0, 0, 'L', 1);
        $pdf->Cell(13, 7, number_format($totalQty, 0), 0, 0, 'L', 1);
        $pdf->Cell(11, 7, number_format($totalWeightQty, 0), 0, 0, 'C', 1);
        $pdf->Cell(14, 7, number_format($totalAmount, 0), 0, 1, 'C', 1);
        $pdf->ln(6);

        $pdf->setFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->AddPage();

        // EXPENSES
        if ($permissions[0]->expenses == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'EXPENSE DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $expenses = expense::join('expense_categories', 'expense_categories.exp_cat_id', '=', 'expenses.exp_cat_id')->where('expenses.opening_id', $openingId)->get();
            $totalExpenseAmount = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Category', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Amount', 0, 0, 'L', 1);
            $pdf->Cell(38, 7, 'Details', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($expenses) > 0) {
                foreach ($expenses as $key => $expense) {
                    $totalExpenseAmount += $expense->amount;
                    $pdf->Cell(20, 7, $expense->expense_category, 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($expense->amount,0), 0, 0, 'L', 1);
                    $pdf->Cell(38, 7, $expense->expense_details, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, "Total", 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($totalExpenseAmount,0), 0, 0, 'L', 1);
                $pdf->Cell(38, 7, "", 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        // CASH IN 
        if ($permissions[0]->cash_in == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'CASH-IN DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $cashins = DB::table("sales_cash_in")->where("opening_id", $openingId)->get();
            $totalCashIns = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Date', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Amount', 0, 0, 'L', 1);
            $pdf->Cell(38, 7, 'Narration', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($cashins) > 0) {
                foreach ($cashins as $key => $cashin) {
                    $totalCashIns += $cashin->amount;
                    $pdf->Cell(20, 7, date("d M Y", strtotime($cashin->datetime)), 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($cashin->amount,0), 0, 0, 'L', 1);
                    $pdf->Cell(38, 7, $cashin->narration, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, "Totals", 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($totalCashIns,0), 0, 0, 'L', 1);
                $pdf->Cell(38, 7, "", 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        // CASH OUT
        if ($permissions[0]->cash_out == 1) {
            $pdf->ln(6);
            $pdf->setFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(78, 6, 'CASH-OUT DETAILS', 0, 0, 'C', 1);
            $pdf->ln(6);

            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            $cashouts = DB::table("sales_cash_out")->where("opening_id", $openingId)->get();
            $totalCashOuts = 0;

            $pdf->setFillColor(233, 233, 233);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(20, 7, 'Date', 0, 0, 'L', 1);
            $pdf->Cell(20, 7, 'Amount', 0, 0, 'L', 1);
            $pdf->Cell(38, 7, 'Narration', 0, 1, 'C', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            if (count($cashouts) > 0) {
                foreach ($cashouts as $key => $cashout) {
                    $pdf->Cell(20, 7, date("d M Y", strtotime($cashout->datetime)), 0, 0, 'L', 1);
                    $pdf->Cell(20, 7, number_format($cashout->amount,0), 0, 0, 'L', 1);
                    $pdf->Cell(38, 7, $cashout->narration, 0, 1, 'C', 1);
                }
                $pdf->setFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->Cell(20, 7, "Totals", 0, 0, 'L', 1);
                $pdf->Cell(20, 7, number_format($totalCashOuts,0), 0, 0, 'L', 1);
                $pdf->Cell(38, 7, "", 0, 1, 'C', 1);
                $pdf->ln(6);

                $pdf->setFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
            } else {
                $pdf->Cell(78, 7, "No Record Found", 0, 1, 'C', 1);
            }
        }

        header('Content-Type: application/pdf; charset=utf-8');

        $filePath = storage_path('app/public/declarationpdfs/sales_declaration_report_' . $openingId  . '.pdf');

        // Ensure the 'pdfs' folder exists, if not, create it
        if (!file_exists(storage_path('app/public/declarationpdfs'))) {
            mkdir(storage_path('app/public/declarationpdfs'), 0777, true);
        }

        // Save the PDF to the specified path
        return $pdf->Output('F', $filePath);
    }

    /**
     * Prepare the email with the generated report.
     *
     * @param array $reportData
     * @return void
     */
    private function prepareEmail($openingId)
    {
        $users = new userDetails();
        $dash = new dashboard();
        $date = date("Y-m-d", strtotime("-1 day"));
        $terminals = DB::select("SELECT d.company_id,d.name as company,d.logo,c.branch_id,c.branch_name as branch, b.terminal_name as terminal, a.permission_id,a.terminal_id FROM users_sales_permission a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN branch c on c.branch_id = b.branch_id INNER JOIN company d on d.company_id = c.company_id where a.Email_Reports = 1 and b.status_id = 1");
        foreach ($terminals as $key => $terminal) {
            $emails  = DB::table("branch_emails")->where("branch_id", $terminal->branch_id)->pluck("email");
            if (!empty($emails)) {
                // $emails = implode(",", $emails->toArray());

                // return implode(",",$emails->toArray());
                $settings = DB::table("settings")->where("company_id", $terminal->company_id)->first();
                $settings = !empty($settings) ? json_decode($settings->data) : '';
                $currency = !empty($settings) ? $settings->currency : 'Rs.';
                $opening = SalesOpening::where("terminal_id", $terminal->terminal_id)->where("date", $date)->where("status", 2)->first();
                $companyLogo = "https://retail.sabsoft.com.pk/storage/images/company/" . $terminal->logo;
                if (!empty($opening)) {
                    $permissions = $users->getPermission($terminal->terminal_id);
                    $terminal_name = $users->getTerminalName($terminal->terminal_id);
                    $heads = $dash->getheadsDetailsFromOpeningIdForClosing($opening->opening_id);
                    if (!empty($heads)) {
                        $data = [];
                        $data["permissions"] =  $permissions;
                        $data["terminal"] =  $terminal_name;
                        $data["heads"]  =  $heads;

                        $branchName = $terminal_name[0]->branch_name;
                        $subject = "Sales Declaration Email of " . $terminal_name[0]->branch_name . " (" . $terminal_name[0]->terminal_name . ") ";
                        $declarationNo =  $heads[0]->opening_id;

                        $this->generatePdfReport($terminal->company_id, $terminal->branch_id, $terminal->terminal_id, $openingId);
                        $emails = ["hmadilkhan@gmail.com"]; // ->cc(["hmadilkhan@gmail.com", "syedrazaali10@gmail.com", "humayunshamimbarry@gmail.com"])
                        Mail::to($emails)->send(new DeclarationEmail($branchName, $subject, $declarationNo, $data, $currency, $date, $companyLogo));
                    } // Details not found
                } // Opening Id not found
            } // Email Not found bracket
        } 
    }
}
