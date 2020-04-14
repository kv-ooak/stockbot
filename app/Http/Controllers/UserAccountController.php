<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\UserAccount;
use App\Managers\TradeManager;
use App\Managers\LogManager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAccountController extends Controller {

    public function __construct(JWTAuth $auth) {
        $this->middleware('cors');
        $this->middleware('jwt.auth');
        $this->middleware('jwt.refresh');
        $this->middleware('permission:user-account-api');

        // User and account info
        try {
            $this->user = $auth::parseToken()->toUser();
            $this->user_id = $this->user['id'];
            $account_id = Input::has('account_id') ? Input::get('account_id') : 0;
            $this->account = $account_id > 0 ? UserAccount::getAccount($account_id, $this->user_id) : null;
        } catch (JWTException $e) {
            $this->user_id = 0;
            $this->account = null;
        }
    }

    /**
     * Get account list and get account info from TradeManager
     * 
     * @return type
     */
    public function getAccount() {
        try {
            $result['data'] = [];
            // Get account list
            $result['data']['list'] = UserAccount::getAllAccounts($this->user_id);
            // Get account info from TradeManager
            $result['data']['detail'] = $this->account !== null ? TradeManager::getAccountInfo($this->account->id, $this->user_id) : [];

            $result['status'] = 'ok';

            LogManager::addActionLog($this->user_id, 'Account', 'getAccount', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Account', 'getAccount', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Add or Update account
     * 
     * @return type
     */
    public function postAccount() {
        try {
            $account_name = Input::has('account_name') ? Input::get('account_name') : 'No name';
            $account_desc = Input::has('account_desc') ? Input::get('account_desc') : '';

            if ($this->account !== null) {
                // Update info
                $this->account->account_name = $account_name !== 'No name' ? $account_name : $account->account_name;
                $this->account->account_description = $account_desc != '' ? $account_desc : $account->account_description;
                $this->account->save();
                $result['id'] = $this->account->id;
            } else {
                // Add new account
                $account = new UserAccount;
                $account->user_id = $this->user_id;
                $account->account_name = $account_name;
                $account->account_description = $account_desc;
                $account->save();
                $result['id'] = $account->id;
            }

            $result['status'] = 'ok';

            LogManager::addActionLog($this->user_id, 'Account', 'postAccount', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $result['id']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Account', 'postAccount', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    /**
     * Delete account (using delete_flag, not physical delete)
     * 
     * @return type
     */
    public function deleteAccount() {
        try {
            if ($this->account === null) {
                LogManager::addActionLog($this->user_id, 'Account', 'deleteAccount', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], 'Invalid account');
                return Response::json([
                            'error' => true,
                            'message' => 'Can not delete account',
                            'code' => 500
                                ], 500);
            }

            $this->account->delete_flag = 1;
            $this->account->save();
            $result['status'] = 'ok';

            LogManager::addActionLog($this->user_id, 'Account', 'deleteAccount', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok'], $this->account->id);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Account', 'deleteAccount', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

}
