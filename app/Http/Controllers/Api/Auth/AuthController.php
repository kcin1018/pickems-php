<?php

namespace Pickems\Http\Controllers\Api\Auth;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Pickems\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    use Helpers;

    public function postLogin(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                throw new UnauthorizedHttpException('Email address / password do not match');
            }

            // all good so return the token
            return $this->response->array(compact('token'));
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            throw new HttpException('Unable to login');
        }
    }

    public function refreshToken(Request $request)
    {
        $token = $request->input('token');
        if (!$token) {
            return $this->response->errorBadRequest('Token not provided');
        }
        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $e) {
            return $this->response->errorForbidden('Invalid token provided');
        }

        return $this->response->array(compact('token'));
    }
}
