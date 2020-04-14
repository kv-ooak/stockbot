<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Managers\FileManager;
use App\Managers\DataType;
use App\Managers\LogManager;
use App\TickerBot;
use App\Job;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminController extends Controller {

    public function __construct(JWTAuth $auth) {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
        $this->middleware('jwt.refresh');
        $this->middleware('permission:admin-api');

        // User info
        try {
            $this->user = $auth::parseToken()->toUser();
            $this->user_id = $this->user['id'];
        } catch (JWTException $e) {
            $this->user_id = 0;
        }
    }

    /**
     * 
     * @return type
     */
    public function index() {
        try {
            $result = [];
            $files = FileManager::getFileList();
            $data_type = array(
                DataType::Unknown => "",
                DataType::Ticker => "Ticker List",
                DataType::RawData => "Ticker Raw Data",
                DataType::Quote => "Quote",
            );

            // Job check
            $job_message = null;
            $result['fileList'] = $files;
            $result['jobMessage'] = $job_message;
            $result['dataType'] = $data_type;

            LogManager::addActionLog($this->user_id, 'Admin', 'index', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'index', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Clear table data
     * 
     * @param Request $request
     * @param type $action
     * @return type
     */
    public function clearData(Request $request, $action) {
        try {
            $action_list = [
                'ticker',
                'ticker_data',
                'ticker_quote',
                'ticker_bot',
                'ticker_recommend'
            ];
            if (in_array($action, $action_list)) {
                Artisan::queue("command:ClearTable", [
                    'action' => $action
                ]);
            }

            LogManager::addActionLog($this->user_id, 'Admin', 'clearData', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json([
                        'error' => false,
                        'code' => 200
                            ], 200);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'clearData', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * File Upload
     * 
     * @param Request $request
     * @return type
     */
    public function postUploadFile(Request $request) {
        try {
            $file = $request->file('uploadfile');
            $data_type = $request->input('data_type');
            $result = FileManager::addFile($file, $data_type);

            LogManager::addActionLog($this->user_id, 'Admin', 'postUploadFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok']);
            return $result;
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'postUploadFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Delete file
     * 
     * @param Request $request
     * @param type $filename
     * @return type
     */
    public function postDeleteFile(Request $request, $filename) {
        try {
            $file = FileManager::getFileByName($filename);

            if ($file === null) {
                LogManager::addActionLog($this->user_id, 'Admin', 'postDeleteFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], 'File not found.');
                return Response::json([
                            'error' => true,
                            'message' => 'File not found.',
                            'code' => 404
                                ], 404);
            }

            if (count(Job::all()) > 0) {
                LogManager::addActionLog($this->user_id, 'Admin', 'postDeleteFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], 'Other job is running.');
                return Response::json([
                            'error' => true,
                            'message' => 'Can not delete file while other job is running. Please try again later.',
                            'code' => 403
                                ], 403);
            }

            FileManager::deleteFile($file->filename);

            LogManager::addActionLog($this->user_id, 'Admin', 'postDeleteFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok'], $file->original_filename);
            return Response::json([
                        'error' => false,
                        'message' => 'Action has been sent to queue. Please wait a moment.',
                        'code' => 200
                            ], 200);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'postDeleteFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Import data to database
     * 
     * @param Request $request
     * @param type $filename
     * @return type
     */
    public function importDataFromFile(Request $request, $filename, $truncate) {
        try {
            $file = FileManager::getFileByName($filename);

            /*
              if (count(Job::all()) > 0) {
              return Response::json([
              'error' => true,
              'message' => 'Other job is running. Please try again later.',
              'code' => 403
              ], 403);
              } */

            if ($file === null) {
                LogManager::addActionLog($this->user_id, 'Admin', 'importDataFromFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], 'File not found.');
                return Response::json([
                            'error' => true,
                            'message' => 'File not found.',
                            'code' => 404
                                ], 404);
            }

            if ($file->data_type == DataType::Unknown) {
                LogManager::addActionLog($this->user_id, 'Admin', 'importDataFromFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], 'File has no action.');
                return Response::json([
                            'error' => true,
                            'message' => 'File has no action.',
                            'code' => 404
                                ], 404);
            }

            switch ($file->data_type) {
                case DataType::Ticker:
                    Artisan::queue("command:ImportTicker", [
                        'filename' => $file->original_filename,
                        '--truncate' => $truncate
                    ]);
                    break;
                case DataType::RawData:
                    Artisan::queue("command:ImportData", [
                        'filename' => $file->original_filename,
                        '--truncate' => $truncate
                    ]);
                    break;
                case DataType::Quote:
                    Artisan::queue("command:ImportQuote", [
                        'filename' => $file->original_filename,
                        '--truncate' => $truncate
                    ]);
                    break;
            }

            LogManager::addActionLog($this->user_id, 'Admin', 'importDataFromFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok'], $file->original_filename);
            return Response::json([
                        'error' => false,
                        'message' => 'Action has been sent to queue. Please wait a moment.',
                        'code' => 200
                            ], 200);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'importDataFromFile', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Calculate data
     * 
     * @param Request $request
     * @param type $action
     * @return type
     */
    public function postCalculate(Request $request, $action, $date) {
        try {
            /*
              if (count(Job::all()) > 0) {
              return Response::json([
              'error' => true,
              'message' => 'Other job is running. Please try again later.',
              'code' => 403
              ], 403);
              } */

            switch ($action) {
                case 'bot':
                    Artisan::queue("command:CalculateBot");
                    break;
                case 'recommend':
                    $date = TickerBot::checkDate($date) ? $date : TickerBot::getLatestDate();
                    Artisan::queue("command:CalculateRecommend", ['--date' => $date, '--clear' => 1]);
                    break;
                case 'recommend10days':
                    $dateList = TickerBot::getLatestDates(10);
                    foreach ($dateList as $d) {
                        Artisan::queue("command:CalculateRecommend", ['--date' => $d->date]);
                    }
                    break;
                case 'recommend10days_clear':
                    $dateList = TickerBot::getLatestDates(10);
                    foreach ($dateList as $d) {
                        Artisan::queue("command:CalculateRecommend", ['--date' => $d->date, '--clear' => 1]);
                    }
                    break;
                case 'recommend42days':
                    $dateList = TickerBot::getLatestDates(42);
                    foreach ($dateList as $d) {
                        Artisan::queue("command:CalculateRecommend", ['--date' => $d->date]);
                    }
                    break;
                case 'recommend42days_clear':
                    $dateList = TickerBot::getLatestDates(42);
                    foreach ($dateList as $d) {
                        Artisan::queue("command:CalculateRecommend", ['--date' => $d->date, '--clear' => 1]);
                    }
                    break;
            }

            LogManager::addActionLog($this->user_id, 'Admin', 'postCalculate', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok'], $action);
            return Response::json([
                        'error' => false,
                        'message' => 'Action has been sent to queue. Please wait a moment.',
                        'code' => 200
                            ], 200);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'postCalculate', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Clear cache
     * 
     * @param Request $request
     * @return type
     */
    public function clearCache(Request $request) {
        try {
            Cache::flush();

            LogManager::addActionLog($this->user_id, 'Admin', 'clearCache', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json([
                        'error' => false,
                        'code' => 200
                            ], 200);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'clearCache', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get Job List
     * 
     * @return type
     */
    public function getJobList() {
        try {
            $result = [];
            $jobs = Job::all();

            $result['jobs'] = $jobs;

            LogManager::addActionLog($this->user_id, 'Admin', 'getJobList', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'getJobList', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Delete job
     * 
     * @param type $job_id
     * @return type
     */
    public function deleteJob($job_id) {
        try {
            if ($job_id > 0) {
                Job::where('id', $job_id)
                        ->where('reserved', 0)
                        ->delete();
            } else {
                Job::where('reserved', 0)
                        ->delete();
            }

            LogManager::addActionLog($this->user_id, 'Admin', 'deleteJob', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok'], $job_id);
            return Response::json([
                        'error' => false,
                        'code' => 200
                            ], 200);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Admin', 'deleteJob', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

}
