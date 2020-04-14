<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Managers\LogManager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LogController extends Controller {

    public function __construct(Request $request, JWTAuth $auth) {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
        //$this->middleware('jwt.refresh');
        $this->middleware('permission:admin-api');

        //Datatable parameter
        $this->draw = $request->input('draw');
        //paging parameter
        $this->start = $request->input('start');
        $this->length = $request->input('length');
        //sorting parameter
        $this->sortColumn = $request->input('columns')[$request->input('order')[0]['column']]['data'];
        $this->sortColumnDir = $request->input('order')[0]['dir'];
        //filter parameter
        $this->searchValue = $request->input('search')['value'];

        $this->pageSize = $this->length != null ? $this->length : 0;
        $this->skip = $this->start != null ? $this->start : 0;

        // User info
        try {
            $this->user = $auth::parseToken()->toUser();
            $this->user_id = $this->user['id'];
        } catch (JWTException $e) {
            $this->user_id = 0;
        }
    }

    /**
     * Get queue log
     * 
     * @return type
     */
    public function getQueueLog() {
        try {
            //DB query
            $_data = LogManager::getQueueLog($this->searchValue)->toArray();
            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            //LogManager::addActionLog($this->user_id, 'Log', 'queue', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Log', 'queue', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Get action log
     * 
     * @return type
     */
    public function getActionLog() {
        try {
            //DB query
            $_data = LogManager::getActionLog($this->searchValue)->toArray();
            //Sort
            if ($this->sortColumn != null && strlen($this->sortColumn) > 0 && $this->sortColumnDir != null && strlen($this->sortColumnDir)) {
                usort($_data, function($a, $b) {
                    return $this->sortColumnDir === 'asc' ? $a[$this->sortColumn] > $b[$this->sortColumn] : $a[$this->sortColumn] < $b[$this->sortColumn];
                });
            }
            $recordsTotal = count($_data);
            $data = array_slice($_data, $this->skip, $this->pageSize);
            //Result
            $result = Array(
                'draw' => $this->draw,
                'recordsFiltered' => $recordsTotal,
                'recordsTotal' => $recordsTotal,
                'data' => $data,
            );

            //LogManager::addActionLog($this->user_id, 'Log', 'action', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Log', 'action', LogManager::$ACTION_TYPE['Admin'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

}
