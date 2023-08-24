<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\CategoryCollection;

class CategoryController extends Controller
{
    use ApiResponser;

    /**
     * Category API endpoints.
     *
     * @OA\Get(
     *     path="/api/v1/categories",
     *     tags={"Category"},
     *     summary="List all categories",
     *     operationId="index",
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
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->sortBy) {

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

        return new CategoryCollection($query->paginate($request->limit ?? 10));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/category/create",
     *      tags={"Category"},
     *      summary="Create a new category",
     *      operationId="storeCategory",
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
    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255'
        ]);

        if($validator->fails()){
            return $this->generalApiResponse(422, [], null, $validator->messages());
        }

        $inputedDataArray = $validator->validated();
        $inputedDataArray += ['slug' => str_replace(' ', '-', $request->title)];

        $newCategory = Category::create($inputedDataArray);

        return $this->generalApiResponse(200, ['uuid' => $newCategory->uuid]);
    }

    public function update($uuid, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255'
        ]);

        if($validator->fails()){
            return $this->generalApiResponse(422, [], null, $validator->messages());
        }

        $category = Category::where('uuid', $uuid)->firstOrFail();

        $inputedDataArray = $validator->validated();
        $inputedDataArray += ['slug' => str_replace(' ', '-', $request->title)];

        $category->update($inputedDataArray);

        return $this->generalApiResponse(200, [$category]);
    }

    /**
     * Category API endpoints.
     *
     * @OA\Get(
     *     path="/api/v1/category/{uuid}",
     *     tags={"Category"},
     *     summary="Fetch a category",
     *     operationId="show",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="query",
     *         description="uuid of expected category",
     *         required=true,
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
    public function show($uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();

        return $this->generalApiResponse(200, [$category]);
    }

    public function delete($uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();

        $category->delete();

        return $this->generalApiResponse(200);
    }
}
