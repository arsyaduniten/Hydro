<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Gates;
use App\Events\WaterLevelChanged;

class SeedWater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:water';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update water level simulation';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $gates = Gates::all();
        foreach ($gates as $gate) {
            $operator = rand(0,1);
            if ($operator == 0) {
                $gate->water_level += rand(1, 10);
            } else if ($operator == 1){
                $gate->water_level -= rand(1, 10);
            }
            if ($gate->water_level > 50) {
                $gate->gate_open = 0;
            } else {
                $gate->gate_open = 1;
            }
            $gate->save();
            broadcast(new WaterLevelChanged($gate));
        }
    }
}
