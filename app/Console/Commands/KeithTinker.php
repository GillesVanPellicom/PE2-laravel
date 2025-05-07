<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class KeithTinker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keith:tinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $package = \App\Models\Package::find(1);
        $package2 = \App\Models\Package::find(2);
        $package3 = \App\Models\Package::find(3);
        for($i = 0; $i < 16; $i++) {
            $package->fakeMove();
            $package2->fakeMove();
            $package3->fakeMove();
        }
    }
}
