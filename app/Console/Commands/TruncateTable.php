<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Ticker;
use App\TickerBot;
use App\TickerData;
use App\TickerQuote;
use App\TickerRecommend;
use App\Managers\LogManager;

class TruncateTable extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ClearTable {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear table data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        try {
            Log::debug("==== START Truncate Table ====");
            $action = $this->argument('action');
            LogManager::addQueueLog('ClearTable', 'table: ' . $action, LogManager::$QUEUE_STATUS['Start'], '');
            switch ($action) {
                case 'ticker':
                    Ticker::truncate();
                    break;
                case 'ticker_data':
                    TickerData::truncate();
                    break;
                case 'ticker_quote':
                    TickerQuote::truncate();
                    break;
                case 'ticker_bot':
                    TickerBot::truncate();
                    break;
                case 'ticker_recommend':
                    TickerRecommend::truncate();
                    break;
                default:
                    break;
            }
            LogManager::addQueueLog('ClearTable', 'table: ' . $action, LogManager::$QUEUE_STATUS['End'], '');
        } catch (\Exception $e) {
            Log::debug("==== ERROR Truncate Table ====" . $e->getMessage());
            LogManager::addQueueLog('ClearTable', '', LogManager::$QUEUE_STATUS['Error'], $e->getMessage());
        }
    }

}
