<?php

namespace fase2\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\ExecJobCron;
use fase2\Department;
use fase2\Purchase;
use fase2\Sale;
use fase2\HistoryBalance;
use Illuminate\Support\Facades\DB;

class LogHistoryBlance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:history_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se registra el corte del balance cada mes';

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
        //Log::info('Mi Comando Funciona!');
        $start = Carbon::now()->subDays(1)->startOfMonth();
		$end = Carbon::now()->subDays(1)->endOfMonth();

        $departaments = Department::all();
	    foreach ($departaments as $_departmanet){ 
            $HistoryBalance = new HistoryBalance;
            $HistoryBalance->department_id =  $_departmanet->id;
            
			$purchaseTotal = Purchase::where('department_id',$_departmanet->id)
            ->where('is_paid',true)
            ->where('updated_at','>=', $start->toDateTimeString())
            ->where('updated_at','<=', $end->toDateTimeString())
			->sum('amount');

            $salesTotal = Sale::where('department_id',$_departmanet->id)
            ->where('is_paid',true)
            ->where('updated_at','>=', $start->toDateTimeString())
            ->where('updated_at','<=', $end->toDateTimeString())
            ->sum('amount');

            $HistoryBalance->entry = intval($salesTotal);
            $HistoryBalance->egress =  intval($purchaseTotal);
            $HistoryBalance->utility =  ($salesTotal - $purchaseTotal);

            $HistoryBalance->save();

            Log::info('Envoy ran @ ' . Carbon::now());
            Mail::to("eaguilar.arrezola@gmail.com")->send(new ExecJobCron(Carbon::now()));

		}

    }
}
