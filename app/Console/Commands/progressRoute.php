<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use Exception;
use App\Helpers\ConsoleHelper;


class progressRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:progress {amount} {steps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Progresses the given amount of packages a given amount of steps';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $amount = (int) $this->argument('amount') ?? 1;
        $steps = (int) $this->argument('steps') ?? 1;

        for ($i = 1; $i <= $amount; $i++) {
            ConsoleHelper::task("Processing package #{$i}", function () use ($i, $steps) {
                $package = Package::find($i);
                try {
                    for ($j = 0; $j < $steps; $j++) {
                        $package->fakeMove();
                    }
                } catch (Exception $e) {
                    ConsoleHelper::error($e->getMessage());
                }
            });
        }
    }
}
