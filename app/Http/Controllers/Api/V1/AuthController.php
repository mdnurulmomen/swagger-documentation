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

class AuthController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *      path="/api/v1/user/login",
     *      tags={"User"},
     *      summary="Login an User account",
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
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
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
