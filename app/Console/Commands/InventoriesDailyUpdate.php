<?php

namespace App\Console\Commands;

use App\LotCode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InventoriesDailyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventories:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all inventories a.k.a lot codes on its expiring balance day and flags';

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
		//Step 0 update days to expiry on all inventory
	$datetime = date('[Y-m-d H:i:s]');
        LotCode::where([
			['balance', '>', 0],
			['fr_lotcode_flag','<>', 5],
			])
			->whereNotNull('expiry_date')
			->update(['daystoexpiry' => DB::raw("extract('day' from date_trunc('day',expiry_date::date - now()))")]);

		//Step 1, not expired item with balance and flag is incorrect
		LotCode::where([
			['daystoexpiry','>',0],
			['balance','>=',1],
			])
			->whereNotIn('fr_lotcode_flag',[1,5])
			->orwhereNull('fr_lotcode_flag')
			->update(['fr_lotcode_flag' => 1]);

		//Step 2, not expired item with no balance and flag is incorrect
		LotCode::where([
			['daystoexpiry','>',0],
			['balance','<=',0],
			])
			->whereNotIn('fr_lotcode_flag',[2,5])
			->orwhereNull('fr_lotcode_flag')
			->update(['fr_lotcode_flag' => 2]);		
			
		//Step 3, expired item and with balance and flag is incorrect
		LotCode::where([
			['daystoexpiry','<=',0],
			['balance','>',0],
			])
			->whereNotIn('fr_lotcode_flag',[3,5])
			->orwhereNull('fr_lotcode_flag')
			->update(['fr_lotcode_flag' => 3]);
			
		//Step 4, expired item with no balance and flag is incorrect
		LotCode::where([
			['daystoexpiry','<=',0],
			['balance','<=',0],
			])
			->whereNotIn('fr_lotcode_flag',[4,5])
			->orwhereNull('fr_lotcode_flag')
			->update(['fr_lotcode_flag' => 4]);			

		
			
		//['fr_lotcode_flag','=',1],])

		
		$this->info("$datetime cron.INFO: Inventories expiry updated successfully,command:inventories:update");
    }
}
