<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\TickerQuote;
use App\Managers\FileManager;
use App\Managers\LogManager;

class ImportTickerQuote extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportQuote {filename} {--truncate=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import quote from csv';

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
            Log::debug("==== START Import Ticker Quote ====");
            $truncate = $this->option('truncate');
            $filename = $this->argument('filename');
            LogManager::addQueueLog('ImportQuote', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['Start'], '');
            if ($truncate) {
                TickerQuote::truncate();
            }

            $file_path = FileManager::getFilePathByOriginalName($filename);

            if (isset($file_path)) {
                $pdo = DB::connection()->getPdo();
                DB::connection()->disableQueryLog();
                $file_path = str_replace("\\", "\\\\", $file_path);
                $query = "LOAD DATA LOCAL INFILE'" . $file_path . "' INTO TABLE ticker_quotes FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' (TICKER, DATE, HOUR, BID, PRICE, ASK, VOLUME, TOTAL_VOLUME, STATUS)";
                $pdo->exec($query);

                DB::beginTransaction();
                $quoteData = TickerQuote::getUncalculatedData();
                foreach ($quoteData as $quote) {
                    $quote->value = $quote->price * $quote->volume;
                    $quote->save();
                }

                DB::commit();
                LogManager::addQueueLog('ImportQuote', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['End'], '');
                Log::debug("==== END Import Ticker Quote ====");
            } else {
                LogManager::addQueueLog('ImportQuote', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['Error'], 'File does not exist');
                Log::debug("==== END Import Ticker Quote - No file ====");
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug("==== ERROR Import Ticker Quote ====" . $e->getMessage());
            LogManager::addQueueLog('ImportQuote', '', LogManager::$QUEUE_STATUS['Error'], $e->getMessage());
        }
    }

}
