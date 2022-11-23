<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SaaSJobScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:schedule-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To run scheduler for all clients in saas';

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
        $exitCodes = [];

        DB::connection('system')->table('clients')->orderBy('id')->chunk(10, function ($clients) use ($exitCodes) {
            foreach ($clients as $client) {

                $this->info("Start Running: " . $client->workspace);

                $process = new Process('php artisan schedule:run ', null, json_decode($client->config, 1));

                $process->run();

                if (!$process->isSuccessful()) {
                    $exitCodes[] = $process;
                }

                $this->line($process->getOutput());

            }
            return false;
        });

        if (count($exitCodes) === 0) {
            $this->warn("Command was executed on all clients.");
        }
    }
}
