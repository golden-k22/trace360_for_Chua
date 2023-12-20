<?php

namespace App\Console\Commands;

use App\LotCode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LotCodeNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventories:new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'When new inventories a.k.a lotcode record inserted, check and update daystoexpiry column';

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
	$datetime = date('[Y-m-d H:i:s]');

	LotCode::whereNull('daystoexpiry')
			->update(['daystoexpiry' => DB::raw("extract('day' from date_trunc('day',expiry_date::date - now()))")]);
	$this->info("$datetime cron.INFO: Inventories daystoexpiry updated, command:inventories:new");
    }
}
