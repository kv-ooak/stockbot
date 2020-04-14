<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\TickerData;
use App\Managers\FileManager;
use App\Managers\LogManager;

class ImportTickerData extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ImportData {filename} {--truncate=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import raw data from csv';

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
            Log::debug("==== START Import Ticker Data ====");
            $truncate = $this->option('truncate');
            $filename = $this->argument('filename');
            LogManager::addQueueLog('ImportData', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['Start'], '');
            if ($truncate) {
                TickerData::truncate();
            }

            $file_path = FileManager::getFilePathByOriginalName($filename);

            if (isset($file_path)) {
                $pdo = DB::connection()->getPdo();
                DB::connection()->disableQueryLog();
                $file_path = str_replace("\\", "\\\\", $file_path);
                $query = "LOAD DATA LOCAL INFILE'" . $file_path . "' INTO TABLE ticker_data FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' (TICKER, DATE, OPEN, HIGH, LOW, CLOSE, VOLUME)";
                $pdo->exec($query);
                LogManager::addQueueLog('ImportData', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['End'], '');
                Log::debug("==== END Import Ticker Data ====");
            } else {
                LogManager::addQueueLog('ImportData', 'filename: ' . $filename . ' - truncate: ' . $truncate, LogManager::$QUEUE_STATUS['Error'], 'File does not exist');
                Log::debug("==== END Import Ticker Data - No file ====");
            }
        } catch (\Exception $e) {
            Log::debug("==== ERROR Import Ticker Data ====" . $e->getMessage());
            LogManager::addQueueLog('ImportData', '', LogManager::$QUEUE_STATUS['Error'], $e->getMessage());
        }
    }

}
