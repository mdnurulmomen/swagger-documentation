<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\UserCollection;

class AdminController extends Controller
{
    use ApiResponser;

    /**
     * Admin API endpoints.
     *
     * @OA\Get(
     *     path="/api/v1/admin/user-listing",
     *     tags={"Admin"},
     *     summary="List all users",
     *     operationId="getUserList",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page Number of Pagination",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of elements at per page when paginating",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Name of the field for sorting",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         description="Expected order of data to search users",
     *         @OA\Schema(
     *             type="boolean",
     *             enum={true, false},
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="marketing",
     *         in="query",
     *         description="Expected option to search users",
     *         @OA\Schema(
     *             type="boolean",
     *             enum={true, false},
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="Expected first name to search users",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Expected email to search users",
     *         @OA\Schema(
     *             type="email",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="Expected phone number to search users",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="Expected address to search users",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
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
    public function getUserList(Request $request)
    {
        $query = User::query();

        if ($request->first_name) {

            $query->where('first_name', 'like', "%$request->first_name%");

        }

        if ($request->email) {

            $query->where('email', 'like', "%$request->email%");

        }

        if ($request->phone) {

            $query->where('phone_number', 'like', "%$request->phone%");

        }

        if ($request->address) {

            $query->where('address', 'like', "%$request->address%");

        }

        if ($request->created_at) {

            $query->whereDate('created_at', date('Y-m-d', strtotime($request->created_at)));

        }

        if (filter_var($request->marketing, FILTER_VALIDATE_BOOLEAN)) {

            $query->where('is_marketing', filter_var($request->marketing, FILTER_VALIDATE_BOOLEAN));

        }

        if ($request->sortBy && Schema::connection("mysql")->hasColumn('users', $request->sortBy)) {

            if (filter_var($request->desc, FILTER_VALIDATE_BOOLEAN)) {

                $query->orderByDesc($request->sortBy);

            }
            else {

                $query->orderBy($request->sortBy);

            }

        }

        elseif (filter_var($request->desc, FILTER_VALIDATE_BOOLEAN)) {

            $query->latest();

        }

        return new UserCollection($query->paginate($request->limit ?? 10));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/create",
     *      tags={"Admin"},
     *      summary="Create an Admin account",
     *      operationId="storeUser",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *         required=true,
     *         description="Request properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"first_name","last_name", "email", "password", "password_confirmation", "address", "phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="Admin firstname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="Admin lastname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="Admin email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Admin password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     description="Admin password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     description="Admin main address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="Admin main phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_marketing",
     *                     description="Admin marketing preference",
     *                     type="boolean",
     *                     enum={true, false}
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
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
            'avatar' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'is_marketing' => 'nullable|boolean'
        ]);

        if($validator->fails()){
            return $this->generalApiResponse(422, [], null, $validator->messages());
        }

        $inputedDataArray = $validator->validated();


        if ($this->guard()->user()->is_admin) {

            $inputedDataArray += ['is_admin' => 1];

        }

        $newUser = User::create($inputedDataArray);

        return $this->generalApiResponse(200, [$newUser]);
    }

    /**
     * Update an existing user.
     *
     * @OA\Put(
     *     path="/api/v1/admin/user-edit/{uuid}",
     *     tags={"Admin"},
     *     summary="Update an expected user",
     *     operationId="updateUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="uuid of expected user to update",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Input data properties",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"first_name","last_name", "email", "password", "password_confirmation", "address", "phone_number"},
     *                 @OA\Property(
     *                     property="first_name",
     *                     description="User firstname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     description="User lastname",
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
     *                 @OA\Property(
     *                     property="address",
     *                     description="User main address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     description="User main phone number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preference",
     *                     type="boolean",
     *                     enum={true, false}
     *                 )
     *             )
     *         )
     *     ),
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
    public function updateUser($uuid, Request $request)
    {
        try {

            $user = User::where('uuid', $uuid)->firstOrFail();

        } catch (\Throwable $th) {

            return $this->generalApiResponse(200, [], 'User not found');

        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'required|string|min:8|max:255|confirmed',
            'avatar' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'is_marketing' => 'nullable|boolean'
        ]);

        if($validator->fails()){
            return $this->generalApiResponse(422, [], null, $validator->messages());
        }

        $user->update($validator->validated());

        return $this->generalApiResponse(200, [$user]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/user-delete/{uuid}",
     *     tags={"Admin"},
     *     summary="Deletes a User account",
     *     operationId="deleteUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="uuid of expected user",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
    public function deleteUser($uuid)
    {
        try {

            $user = User::where('uuid', $uuid)->firstOrFail();

        } catch (\Throwable $th) {

            return $this->generalApiResponse(200, [], 'User not found');

        }

        $user->delete();

        return $this->generalApiResponse(200);
    }
}
