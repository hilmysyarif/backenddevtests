<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\RegistrationFormRequest;
class APIController extends Controller
{
    /**
     * @var bool
     */
    public $loginAfterSignUp = true;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $input          = $request->only('username', 'password');
        $token          = null;

        if (!$token = JWTAuth::attempt($input)) {
            return response()->json([
                'error'     => true,
                'message'   => 'Invalid Username or Password',
            ], 401);
        }
        $user           = \Auth::user();
        $user->token    = $token;
        $user->save();
        return response()->json([
            'error'   => false,
            'data'    => $user,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    /**
     * @param RegistrationFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $checkUser          = User::where('username', $request->username)->first();
        if(empty($checkUser)){
            $user               = new User();
            $user->_user_id      = $request->_user_id;
            $user->username     = $request->username;
            $user->user_level   = $request->user_level;
            $user->password     = bcrypt($request->password);
            $user->save();
            if ($this->loginAfterSignUp) {
                return $this->login($request);
            }
    
            return response()->json([
                'error'     => false,
                'success'   => true,
                'data'      => $user
            ], 200);
        }else{
            return response()->json([
                'error'     => true,
                'success'   => false,
                'message'   => 'Sorry, the user already exists'
            ], 200);
        }

    }
}
