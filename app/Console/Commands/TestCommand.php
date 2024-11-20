<?php

namespace App\Console\Commands;

use App\dashboard;
use App\Mail\DeclarationEmail;
use App\Models\SalesOpening;
use App\Services\DeclarationEmailService;
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
        $emailService = new DeclarationEmailService();
        $openingId = 0;
        if (count($params) > 0) {
            $openingId = $params[0];
        }
        if ($openingId > 0) {
            $this->info("opening Id : ".$openingId);
            $emailService->generateCompleteReportAndSendEmail($openingId);
            $this->info("Email Sent : ".$openingId);
        }
        return 1;
    }
}
