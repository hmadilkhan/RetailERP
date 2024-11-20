<?php

namespace App\Console\Commands;

use App\dashboard;
use App\Mail\DeclarationEmail;
use App\Models\SalesOpening;
use App\userDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:run {data*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $params = $this->argument('data');
        $openingId = 0;
        if (!empty($params)) {
            $openingId = $params[0];
        }
        if ($openingId > 0) {
            $users = new userDetails();
            $dash = new dashboard();
            $opening = SalesOpening::where("opening_id", $openingId)->where("status", 2)->first();
            $date = $opening->date;
            $terminals = DB::select("SELECT d.company_id,d.name as company,d.logo,c.branch_id,c.branch_name as branch, b.terminal_name as terminal, a.permission_id,a.terminal_id FROM users_sales_permission a INNER JOIN terminal_details b on b.terminal_id = a.terminal_id INNER JOIN branch c on c.branch_id = b.branch_id INNER JOIN company d on d.company_id = c.company_id where a.Email_Reports = 1 and b.status_id = 1 and b.terminal_id = ?", [$opening->terminal_id]);
            foreach ($terminals as $key => $terminal) {
                $emails  = DB::table("branch_emails")->where("branch_id", $terminal->branch_id)->pluck("email");
                if (!empty($emails)) {
                    $settings = DB::table("settings")->where("company_id", $terminal->company_id)->first();
                    $settings = !empty($settings) ? json_decode($settings->data) : '';
                    $currency = !empty($settings) ? $settings->currency : 'Rs.';
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

                            $this->generateCompleteReportForEmail($terminal->company_id, $terminal->branch_id, $terminal->terminal_id, $opening->opening_id);
                            // print($emails);
                            $emails = ["hmadilkhan@gmail.com"];
                            //->cc(["humayunshamimbarry@gmail.com"])
                            Mail::to($emails)->send(new DeclarationEmail($branchName, $subject, $declarationNo, $data, $currency, $date, $companyLogo));
                        } // Details not found
                    } // Opening Id not found
                } // Email Not found bracket
                //
            }
        }
        return 1;
    }
}
