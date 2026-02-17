<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\report;
use App\pdfClass;
use Exception;
use Illuminate\Support\Facades\Mail;

class GenerateMonthlyFbrReportCommand extends Command
{
    protected $signature = 'fbr:generate-monthly';
    protected $description = 'Generate monthly FBR reports on 1st of every month for previous month';
    protected $filesArray = [];

    public function handle()
    {
        try {
            $from = date('Y-m-01', strtotime('first day of last month'));
            $to = date('Y-m-t', strtotime('last day of last month'));

            $this->info("Generating FBR reports for: {$from} to {$to}");

            $totalReports = DB::table("fbr_details")
                ->join("branch", "branch.branch_id", "=", "fbr_details.branch_id")
                ->join("company", "company.company_id", "=", "branch.company_id")
                ->select("branch.branch_id", "branch.branch_email", "branch.branch_name", "company.company_id", "company.name as company_name", "company.ptcl_contact", "company.address", "company.logo")
                ->where("fbr_details.status", 1)
                ->get();

            if ($totalReports->isEmpty()) {
                $this->info('No active FBR reports found.');
                return;
            }

            foreach ($totalReports as $report) {
                try {
                    $this->info("Processing: {$report->company_name} - {$report->branch_name}");
                    $this->saveFbrReport($report, $from, $to);
                } catch (Exception $e) {
                    $this->error("Error processing {$report->company_name}: {$e->getMessage()}");
                }
            }

            $this->info('Monthly FBR reports generated successfully.');
        } catch (Exception $e) {
            $this->error("Command execution failed: {$e->getMessage()}");
        }
    }

    private function saveFbrReport($report, $from, $to)
    {
        // changes in report model
        $reportmodel = new report();

        if (!file_exists(public_path('storage/images/company/qrcode.png'))) {
            $qrcodetext = $report->company_name . " | " . $report->ptcl_contact . " | " . $report->address;
            \QrCode::size(200)
                ->format('png')
                ->generate($qrcodetext, Storage::disk('public')->put("images/company/", "qrcode.png"));
        }

        $pdf = new pdfClass();

        $pdf->AliasNbPages();
        $pdf->AddPage();

        //first row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Cell(105, 0, "Company Name:", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'L');

        //second row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, 0, '', 0, 0);
        $pdf->Image(public_path('storage/images/company/' . $report->logo), 12, 10, -200);
        $pdf->Cell(105, 12, "FBR REPORT", 0, 0, 'L');
        $pdf->Cell(50, 0, "", 0, 1, 'R');
        $pdf->Image(public_path('storage/images/company/qrcode.png'), 175, 10, -200);

        //third row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 25, '', 0, 0);
        $pdf->Cell(105, 25, "Contact Number:", 0, 0, 'L');
        $pdf->Cell(50, 25, "", 0, 1, 'L');

        //forth row
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(35, -15, '', 0, 0);
        $pdf->Cell(105, -15, $report->ptcl_contact, 0, 0, 'L');
        $pdf->Cell(50, -15, "", 0, 1, 'L');

        //fifth row
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 28, '', 0, 0);
        $pdf->Cell(105, 28, "Company Address:", 0, 0, 'L');
        $pdf->Cell(50, 28, "", 0, 1, 'L');

        //sixth row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, -18, '', 0, 0);
        $pdf->Cell(105, -18, $report->address, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, -18, "Generate Date:  " . date('Y-m-d'), 0, 1, 'R');

        //filter section
        $fromdate = date('F-d-Y', strtotime($from));
        $todate = date('F-d-Y', strtotime($to));

        $pdf->ln(12);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(190, 10, $fromdate . ' through ' . $todate, 0, 1, 'C');

        //report name
        $pdf->ln(1);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, 'FBR Report', 'B,T', 1, 'L');
        $pdf->ln(1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->setFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(10, 7, 'S.No', 'B', 0, 'L', 1);
        $pdf->Cell(30, 7, 'Sales ID', 'B', 0, 'C', 1);
        $pdf->Cell(45, 7, 'FBR Inv Number', 'B', 0, 'L', 1);
        $pdf->Cell(25, 7, 'Date', 'B', 0, 'C', 1);
        $pdf->Cell(25, 7, 'Sales', 'B', 0, 'C', 1);
        $pdf->Cell(20, 7, 'S.Tax', 'B', 0, 'C', 1);
        $pdf->Cell(35, 7, 'Total Amount', 'B', 1, 'C', 1);

        //total variables
        $totalqty = 0;
        $totalactualamount = 0;
        $totalsalestax = 0;
        $totalamount = 0;
        $price = 0;

        $terminals = $reportmodel->get_terminals_by_branch($report->branch_id);
        foreach ($terminals as $values) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 10, "Terminal Name: " . $values->terminal_name, 0, 1, 'L');
            $details = $reportmodel->sales($values->terminal_id, $from, $to);
            foreach ($details as $key => $value) {
                $actualAmount = 0;
                $salesTaxAmount = 0;
                if ($value->actual_amount == 0) {
                    $actualAmount = $value->total_amount - $value->sales_tax_amount;
                } else {
                    $actualAmount = $value->actual_amount;
                }

                $totalqty = $totalqty + 1;
                $totalactualamount = $totalactualamount + $actualAmount;
                $totalsalestax = $totalsalestax + $value->sales_tax_amount;
                $totalamount = $totalamount + $value->total_amount;

                $pdf->SetFont('Arial', '', 10);
                $pdf->setFillColor(232, 232, 232);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(10, 6, ++$key, 0, 0, 'L', 1);
                $pdf->Cell(30, 6, $value->id, 0, 0, 'C', 1);
                $pdf->Cell(45, 6, $value->fbrInvNumber, 0, 0, 'L', 1);
                $pdf->Cell(25, 6, date("d M Y", strtotime($value->date)), 0, 0, 'C', 1);
                $pdf->Cell(25, 6, number_format($actualAmount, 2), 0, 0, 'C', 1);
                $pdf->Cell(20, 6, number_format($value->sales_tax_amount, 2), 0, 0, 'C', 1);
                $pdf->Cell(35, 6, number_format($value->total_amount, 2), 0, 1, 'C', 1);
                $pdf->ln(1);
            }
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(55, 7, "", 'B,T', 0, 'L');
            $pdf->Cell(20, 7, "", 'B,T', 0, 'C');
            $pdf->Cell(35, 7, '', 'B,T', 0, 'C');
            $pdf->Cell(25, 7, number_format($totalactualamount, 2), 'B,T', 0, 'C');
            $pdf->Cell(20, 7, number_format($totalsalestax, 2), 'B,T', 0, 'C');
            $pdf->Cell(35, 7, number_format($totalamount, 2), 'B,T', 1, 'C');
        }
        $fileName = 'FBR_REPORT_' . date("M", strtotime($from)) . "_" . $report->company_name . '.pdf';
        $filePath = storage_path('app/public/pdfs/') . $fileName;
        array_push($this->filesArray, storage_path('app/public/pdfs/' . $fileName));
        // //save file
        $pdf->Output($filePath, 'F');
        $this->sendSingleEmail($from, $report, "FBR Report", $filePath);
    }

    public function sendSingleEmail($from, $report, $reportname, $file)
    {
        $data["email"] = "hmadilkhan@gmail.com"; // $report->branch_email;
        $data["title"] = $reportname;
        $data["body"] = $report;
        $data["from"] = $from;

        Mail::send('emails.automaticemail', $data, function ($message) use ($data, $file) {
            $message->to($data["email"], "Sabify")
                ->cc(['faizanakramkhanfaizan@gmail.com'])
                ->subject($data["title"]);
            $message->attach($file);
        });
    }
}
