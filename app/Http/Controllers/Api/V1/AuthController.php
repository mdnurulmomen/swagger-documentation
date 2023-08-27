<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\V1\LoginRequest;

class AuthController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *      path="/api/v1/user/login",
     *      tags={"User"},
     *      summary="Login a User account",
     *      operationId="login",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Page not found"
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *      )
     *  )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if ($request->route()->named('admin.login')) {

            $credentials += ['is_admin' => 1];

        }

        if ($token = $this->guard()->attempt($credentials)) {

            $this->updateLastLoginTime($token);

            return $this->generalApiResponse(200, ['token' => $token]);

        }

        return $this->generalApiResponse(422, [], "Failed to authenticate user", []);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/v1/user/",
     *     tags={"User"},
     *     summary="View self account",
     *     operationId="me",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *     )
     * )
     */
    public function me()
    {
        return $this->generalApiResponse(200, [$this->guard()->user()]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/user",
     *     tags={"User"},
     *     summary="Deletes self account",
     *     operationId="delete",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *     )
     * )
     */
    public function delete()
    {
        $this->guard()->user()->delete();

        return $this->generalApiResponse(200);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/v1/user/logout",
     *     tags={"User"},
     *     summary="Logout a User account",
     *     operationId="logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *     )
     * )
     */
    public function logout()
    {
        $this->guard()->logout();

        return $this->generalApiResponse(200);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/forgot-password",
     *      tags={"User"},
     *      summary="Creates a token to reset a user password",
     *      operationId="getResetToken",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email"},
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Page not found"
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *      )
     *  )
     */
    public function getResetToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users,email'
        ]);

        if($validator->fails()){

            return $this->generalApiResponse(422, [], null, $validator->messages());

        }

        return $this->generalApiResponse(200, ['reset_token' => $this->generateToken($request->email)]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/user/reset-password-token",
     *      tags={"User"},
     *      summary="Reset a user password with a token",
     *      operationId="resetPassword",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"token", "email", "password", "password_confirmation"},
     *                 @OA\Property(
     *                     property="token",
     *                     description="Password reset token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="User password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Page not found"
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *      )
     *  )
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|email|max:255|exists:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
            'token' => [
                'required',
                'string','max:255',
                Rule::exists('password_resets', 'token')
                ->where(function ($query) use ($request) {
                    return $query->where('email', $request->email);
                })
            ]
        ]);

        if($validator->fails()){

            return $this->generalApiResponse(422, [], null, $validator->messages());

        }

        try {

            DB::beginTransaction();

            User::firstWhere('email', $request->email)->update($validator->safe()->only(['password']));

            $this->deleteExistingRecord($request->email);

            DB::commit();

        } catch (\Throwable $th) {

            DB::rollBack();

        }

        return $this->generalApiResponse(200, ['message'=> 'Password has been successfully updated']);

    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/login",
     *      tags={"Admin"},
     *      summary="Login an admin account",
     *      operationId="adminLogin",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     description="User email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User password",
     *                     type="string"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *      ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Page not found"
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *      )
     *  )
     */
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        if($validator->fails()){

            return $this->generalApiResponse(422, [], null, $validator->messages());

        }

        $credentials = $request->only('email', 'password');

        $credentials += ['is_admin' => 1];

        if ($token = $this->guard()->attempt($credentials)) {

            $this->updateLastLoginTime($token);

            return $this->generalApiResponse(200, ['token' => $token]);

        }

        return $this->generalApiResponse(422, [], "Failed to authenticate user", []);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/v1/admin/logout",
     *     tags={"Admin"},
     *     summary="Logout an Admin account",
     *     operationId="adminLogout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server error"
     *     )
     * )
     */
    public function adminLogout()
    {
        $this->guard()->logout();

        return $this->generalApiResponse(200);
    }

    protected function generateToken($email){

        $this->deleteExistingRecord($email);

        $token = Str::random(80);

        $this->storeToken($token, $email);

        return $token;

    }
    protected function storeToken($token, $email){

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
    protected function guard()
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
