<?php

namespace App\Console\Commands;

use App\Http\Services\OlxService;
use Illuminate\Console\Command;

class OlxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olx:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $olx = new OlxService();
        $olx->getAllData();
//        $olx->send();


        return Command::SUCCESS;
    }
}
