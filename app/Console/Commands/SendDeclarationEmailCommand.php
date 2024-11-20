<?php

namespace App\Console\Commands;

use App\Services\DeclarationEmailService;
use Illuminate\Console\Command;

class SendDeclarationEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-declaration-email-command {data*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Declaration Email on Shift Closing ';

    /**
     * Execute the console command.
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
