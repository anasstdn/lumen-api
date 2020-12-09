<?php
namespace App\Http\Controllers;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller {

	protected $jwt;
    public function __construct(JWTAuth $jwt)
    {
            $this->jwt = $jwt;
    }

	public function login(Request $request)
	{
		$this->validate($request, [
			'username' => 'required|string',
			'password' => 'required|string',
		]);
		
		$credentials = $request->only(['username', 'password']);

		if (! $token = Auth::attempt($credentials)) {
			return response()->json(['message' => 'Unauthorized'], 401);
		}

		Auth::user()->update(['api_token' => $token]);

		return $this->respondWithToken($token);
	}

	public function logout(Request $request){
        // $this->jwt->invalidate($this->jwt->getToken());
        // 
        // auth()->logout();

        // return response()->json([
        //     'message' => 'User logged off successfully!'
        // ], 200);
        try {
            // Auth::guard('api')->logout();
            \Auth::user()->update(['api_token' => null]);
            $this->jwt->invalidate($this->jwt->getToken());

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

}