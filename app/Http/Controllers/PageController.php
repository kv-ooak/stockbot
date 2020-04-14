<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use App\Managers\LogManager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class PageController extends Controller {

    public function __construct(JWTAuth $auth) {
        $this->middleware('cors');
        $this->middleware('jwt.auth', ['except' => ['debug']]);
        $this->middleware('jwt.refresh', ['except' => ['debug']]);

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
     * @param Request $request
     * @return type
     */
    public function index() {
        try {
            $result['helloText'] = 'Welcome to Lumine Platform';
            $result['descriptionText'] = 'Feel free to mess around with our awesome functions. Hope you will make tons of MONEY with our site. Enjoy!';

            LogManager::addActionLog($this->user_id, 'Page', 'index', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Ok']);
            return Response::json($result);
        } catch (\Exception $e) {
            LogManager::addActionLog($this->user_id, 'Page', 'index', LogManager::$ACTION_TYPE['User'], LogManager::$ACTION_STATUS['Error'], $e->getMessage());
            return Response::json([
                        'error' => true,
                        'message' => 'Server error. Exception: ' . $e->getMessage(),
                        'code' => 500
                            ], 500);
        }
    }

    public function debug() {
        return view('debug');
    }

}
