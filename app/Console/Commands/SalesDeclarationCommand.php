<?php

namespace App\Console\Commands;

use App\dashboard;
use App\Mail\DeclarationEmail;
use App\Models\SalesOpening;
use App\userDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SalesDeclarationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sales-declaration-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Sales Declaration ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dash = new dashboard();
        $users = new userDetails();
        $date = date("Y-m-d", strtotime("-1 day"));
        $terminals = DB::select("SELECT d.company_id,d.name as company,d.logo,c.branch_id,c.branch_name as branch, b.terminal_name as terminal, a.permission_id,a.terminal_id FROM users_sales_permission a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN branch c on c.branch_id = b.branch_id INNER JOIN company d on d.company_id = c.company_id where a.Email_Reports = 1 and b.status_id = 1");
        foreach ($terminals as $key => $terminal) {
            $emails  = DB::table("branch_emails")->where("branch_id", $terminal->branch_id)->pluck("email");
            if (!empty($emails)) {
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

                        Mail::to($emails)->cc(["hmadilkhan@gmail.com", "syedrazaali10@gmail.com", "humayunshamimbarry@gmail.com"])->send(new DeclarationEmail($branchName, $subject, $declarationNo, $data, $currency, $date, $companyLogo));
                    } // Details not found
                } // Opening Id not found
            } // Email Not found bracket
        } //foreach loop end
    }
}
