<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CategoryReqest;
use Illuminate\Support\Facades\Schema;
use App\Http\Resources\Api\V1\CategoryCollection;

class CategoryController extends Controller
{
    use ApiResponser;

    /**
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

        if ($request->sortBy && Schema::connection("mysql")->hasColumn('categories', $request->sortBy)) {

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
     *                 required={"title"},
     *                 @OA\Property(
     *                     property="title",
     *                     description="Category Title",
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
    public function storeCategory(CategoryReqest $request)
    {
        $inputedDataArray = $request->validated();
        $inputedDataArray += ['slug' => str_replace(' ', '-', $request->title)];

        $newCategory = Category::create($inputedDataArray);

        return $this->generalApiResponse(200, ['uuid' => $newCategory->uuid]);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/v1/category/{uuid}",
     *     tags={"Category"},
     *     summary="Update an existing category",
     *     operationId="updateCategory",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="uuid of expected category to update",
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
     *                 required={"title"},
     *                 @OA\Property(
     *                     property="title",
     *                     description="Category Title",
     *                     type="string"
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
    public function updateCategory($uuid, CategoryReqest $request)
    {
        try {

            $category = Category::where('uuid', $uuid)->firstOrFail();

        } catch (\Throwable $th) {

            return $this->generalApiResponse(200, [], 'Category not found');

        }

        $inputedDataArray = $request->validated();
        $inputedDataArray += ['slug' => str_replace(' ', '-', $request->title)];

        $category->update($inputedDataArray);

        return $this->generalApiResponse(200, [$category]);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/v1/category/{uuid}",
     *     tags={"Category"},
     *     summary="Fetch a category",
     *     operationId="show",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
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
        try {

            $category = Category::where('uuid', $uuid)->firstOrFail();

        } catch (\Throwable $th) {

            return $this->generalApiResponse(200, [], 'Category not found');

        }

        return $this->generalApiResponse(200, [$category]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/category/{uuid}",
     *     tags={"Category"},
     *     summary="Deletes an existing category",
     *     operationId="deleteCategory",
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
    public function deleteCategory($uuid)
    {
        try {

            $category = Category::where('uuid', $uuid)->firstOrFail();

        } catch (\Throwable $th) {

            return $this->generalApiResponse(200, [], 'Category not found');

        }

        $category->delete();

        return $this->generalApiResponse(200);
    }
}
