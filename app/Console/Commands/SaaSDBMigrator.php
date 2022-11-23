<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class SaaSDBMigrator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saas:db-migrate {name=""} {--all : Migrate all clients}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To run migration for all clients in saas';

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
        if($this->option('all')) {
            $exitCodes = [];
            DB::connection('system')->table('clients')->orderBy('id')->chunk(10, function ($clients) use ($exitCodes) {

                foreach ($clients as $client) {

                    $this->info("Start Running: " . $client->workspace);

                    $process = new Process('php artisan migrate --force', null, json_decode($client->config, 1));

                    $process->run();

                    if (!$process->isSuccessful()) {
                        $exitCodes[] = $process;
                    }

                    $this->line($process->getOutput());

                }
                return false;
            });

            if (count($exitCodes) === 0) {
                $this->line("Command was executed on all clients.");
            }
            return;
        }


        $workspace = $this->argument('name');

        $filename = base_path('env/.env.' . $workspace);
        $this->line($filename);
        if (!file_exists($filename)) {
            $this->error("Client not found");
            return;
        }
        $environment = (new \App\Dotenv\Loader($filename))
            ->parse()
            ->toArray();
        $command = "php artisan migrate --force";
        $process = Process::fromShellCommandline($command, base_path(), $environment);

        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("Migrate Error: " . $process->getErrorOutput());
        }

        $this->line($process->getOutput());

    }
}
