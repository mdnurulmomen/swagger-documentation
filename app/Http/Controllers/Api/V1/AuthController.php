<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponser;

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        if($validator->fails()){

            return $this->generalApiResponse(422, [], null, $validator->messages());

        }

        $credentials = $request->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {

            $this->updateLastLoginTime($token);

            return $this->generalApiResponse(200, ['token' => $token]);

        }

        return $this->generalApiResponse(422, [], "Failed to authenticate user", []);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->generalApiResponse(200, [$this->guard()->user()]);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        $this->guard()->user()->delete();

        return $this->generalApiResponse(200);
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return $this->generalApiResponse(200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email'
        ]);

        if($validator->fails()){

            return $this->generalApiResponse(422, [], null, $validator->messages());

        }

        return $this->generalApiResponse(200, ['reset_token' => $this->generateToken($request->email)]);
    }

    protected function generateToken($email){

        $token = Str::random(80);

        $this->storeToken($token, $email);

        return $token;

    }
    protected function storeToken($token, $email){

        $this->deleteExistingRecord($email);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now()
        ]);

    }

    protected function deleteExistingRecord($email)
    {
        DB::table('password_resets')->where('email', $email)->delete();
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

    protected function updateLastLoginTime($token)
    {
        $this->guard()->user()->update([
            'last_login_at' => now()
        ]);
    }
}
