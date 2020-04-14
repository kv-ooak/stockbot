<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthenticateController extends Controller {

    public function __construct() {
        $this->middleware('cors');
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['login', 'status']]);
    }

    /**
     * Login - Authenticate
     * 
     * @param Request $request
     * @return type
     */
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    /**
     * Logout
     * 
     * @param Request $request
     * @return type
     * @throws BadRequestHtttpException
     * @throws AccessDeniedHttpException
     */
    public function logout(Request $request) {
        $token = JWTAuth::getToken();
        if (!$token) {
            throw new BadRequestHtttpException('token_not_provided');
        }
        try {
            JWTAuth::invalidate($token);
        } catch (TokenInvalidException $e) {
            throw new AccessDeniedHttpException('the_token_is_invalid');
        }
        return response()->json(['error' => ''], 200);
    }

    /**
     * Login status check
     * 
     * @param Request $request
     * @return type
     */
    public function status(Request $request) {
        // TODO: return some user info data
        try {
            $result['status'] = JWTAuth::getToken() && JWTAuth::checkOrFail(JWTAuth::getToken());
        } catch (TokenInvalidException $e) {
            $result['status'] = false;
        }
        return Response::json($result);
    }

    /**
     * Refresh token (UNUSED)
     * 
     * @param Request $request
     * @return type
     * @throws BadRequestHtttpException
     * @throws AccessDeniedHttpException
     */
    public function token(Request $request) {
        $token = JWTAuth::getToken();
        if (!$token) {
            throw new BadRequestHtttpException('token_not_provided');
        }
        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $e) {
            throw new AccessDeniedHttpException('the_token_is_invalid');
        }
        return $this->response->withArray(['token' => $token]);
    }

}
