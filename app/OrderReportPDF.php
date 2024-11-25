<?php
namespace App;

use  Crabbly\Fpdf\Fpdf;

class OrderReportPDF extends FPDF
{
    public $branch;
    public $dates;

    function Header()
    {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, ($this->branch == "all" ?  "All Branches" :  $this->branch[0]->company->name . ' - ' .$this->branch[0]->branch_name. ' (' . $this->branch[0]->code . ')' ), 0, 1, 'C'); //,0,'C'
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'From ' . $this->dates['from'] . ' To ' . $this->dates['to'], 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(26, 69, 103);
        $this->SetTextColor(255, 255, 255);
       
        $this->Cell(10, 10, 'Machine / Website #',1, 0, 'C', true);
        if (session('company_id') != 102) {
            $this->Cell(20, 10, "Order #", 1, 0, 'C', true);
        } else {
            $this->Cell(20, 10, "Receipt #",1, 0, 'C', true);
        }
        $this->Cell(25, 10, "Date", 1,0, 'C', true);
        $this->Cell(20, 10, "Time", 1,0, 'C', true);
        $this->Cell(25, 10, "Category", 1,0, 'C', true);
        $this->Cell(30, 10, "Branch", 1,0, 'C', true);
        $this->Cell(30, 10, "Terminal", 1,0, 'C', true);
        $this->Cell(40, 10, "Customer", 1,0, 'C', true);
        $this->Cell(30, 10, "Order Type", 1,0, 'C', true);
        $this->Cell(40, 10, "Payment Method", 1,0, 'C', true);
        $this->Cell(30, 10, "Sales Person", 1,0, 'C', true);

        if (session('company_id') != 102) {
            $this->Cell(25, 10, "Count/Total", 1,0, 'C', true);
        }
        $this->Cell(25, 10, "Delivery Date", 1,0, 'C', true);
        $this->Cell(25, 10, "Status", 1,0, 'C', true);

        if (session('company_id') != 102) {
            $this->Cell(25, 10, "Total Amount", 1,0, 'C', true);
        } else {
            $this->Cell(25, 10,"Actual Amount", 1,0, 'C', true);
        }
        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }


    function TableRow($value, $session)
    {
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(10, 10, $value->web == 1 ? strtoupper($value->url_orderid) : $value->machine_terminal_count, 1,0,'C');
        if ($session('company_id') != 102) {
            $this->Cell(20, 10, $value->id, 1,0,'C');
        } else {
            $this->Cell(20, 10, $value->receipt_no, 1,0,'C');
        }
        $this->Cell(25, 10, date('d M Y', strtotime($value->date)), 1,0,'C');
        $this->Cell(20, 10, date('H:i a', strtotime($value->time)), 1,0,'C');
        $this->Cell(25, 10, $value->web == 1 ? 'Website' : 'POS', 1);
        $this->Cell(30, 10, $value->branchrelation->branch_name, 1);
        $this->Cell(30, 10, $value->terminal->terminal_name, 1);
        $this->Cell(40, 10, !empty($value->customer) ? $value->customer->name : '-', 1);
        $this->Cell(30, 10, !empty($value->mode) ? $value->mode->order_mode : '-', 1);
        $this->Cell(40, 10, !empty($value->payment) ? $value->payment->payment_mode : '-', 1);
        $this->Cell(30, 10, !empty($value->salesperson) ? $value->salesperson->fullname : '-', 1);

        if ($session('company_id') != 102) {
            $this->Cell(25, 10, $value->orderdetails_count . '/' . $value->amount_sum, 1,0,'C');
        }
        $this->Cell(25, 10, date('d-m-Y', strtotime($value->delivery_date)), 1,0,'C');
        $this->Cell(25, 10, $value->orderStatus->order_status_name, 1,0,'C');

        if ($session('company_id') != 102) {
            $this->Cell(25, 10, number_format($value->total_amount,0), 1, 0,'C');
        } else {
            $this->Cell(25, 10, number_format($value->actual_amount,0), 1, 0,'C');
        }
        $this->Ln();
    }
}


