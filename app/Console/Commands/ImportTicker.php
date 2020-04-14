<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Ticker;
use App\Managers\FileManager;
use App\Managers\LogManager;

class ImportTicker extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportTicker {filename} {--truncate=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ticker list from csv';

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
            Log::debug("==== START Import Ticker ====");
            $truncate = $this->option('truncate');
            $filename = $this->argument('filename');
            LogManager::addQueueLog('ImportTicker', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['Start'], '');
            if ($truncate) {
                Ticker::truncate();
            }

            $file_path = FileManager::getFilePathByOriginalName($filename);

            if (isset($file_path)) {
                $pdo = DB::connection()->getPdo();
                DB::connection()->disableQueryLog();
                $file_path = str_replace("\\", "\\\\", $file_path);
                $query = "LOAD DATA LOCAL INFILE '" . $file_path . "' INTO TABLE tickers FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' (TICKER,EXCHANGE,OUTSTANDING,LISTED,TREASURY,FOREIGN_OWNED,EQUITY)";
                $pdo->exec($query);
                LogManager::addQueueLog('ImportTicker', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['End'], '');
                Log::debug("==== END Import Ticker ====");
            } else {
                LogManager::addQueueLog('ImportTicker', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['Error'], 'File does not exist');
                Log::debug("==== END Import Ticker - No file ====");
            }
        } catch (\Exception $e) {
            Log::debug("==== ERROR Import Ticker ====" . $e->getMessage());
            LogManager::addQueueLog('ImportTicker', '', LogManager::$QUEUE_STATUS['Error'], $e->getMessage());
        }
    }

}
