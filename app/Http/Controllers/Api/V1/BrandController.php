<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\Api\V1\BrandRequest;
use App\Http\Resources\Api\V1\BrandCollection;

class BrandController extends Controller
{
    use ApiResponser;

    /**
     *
     * @OA\Get(
     *     path="/api/v1/brands",
     *     tags={"Brand"},
     *     summary="List all brands",
     *     operationId="getBrandList",
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
    public function getBrandList(Request $request)
    {
        $query = Brand::query();

        if ($request->sortBy && Schema::connection("mysql")->hasColumn('brands', $request->sortBy)) {

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

        return new BrandCollection($query->paginate($request->limit ?? 10));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/brand/create",
     *      tags={"Brand"},
     *      summary="Create a new brand",
     *      operationId="storeBrand",
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
     *                     description="Brand Title",
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
    public function storeBrand(BrandRequest $request)
    {
        $inputedDataArray = $request->validated();
        $inputedDataArray += ['slug' => str_replace(' ', '-', $request->title)];

        $newBrand = Brand::create($inputedDataArray);

        return $this->generalApiResponse(200, ['uuid' => $newBrand->uuid]);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/v1/brand/{uuid}",
     *     tags={"Brand"},
     *     summary="Fetch a brand",
     *     operationId="showBrand",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="uuid of expected brand",
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
    public function showBrand($uuid)
    {
        try {

            $brand = Brand::where('uuid', $uuid)->firstOrFail();

        } catch (\Throwable $th) {

            return $this->generalApiResponse(200, [], 'Brand not found');

        }

        return $this->generalApiResponse(200, [$brand]);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/v1/brand/{uuid}",
     *     tags={"Brand"},
     *     summary="Update an existing brand",
     *     operationId="updateBrand",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="uuid of expected brand to update",
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
     *                     description="Brand Title",
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
    public function updateBrand($uuid, BrandRequest $request)
    {
        try {

            $brand = Brand::where('uuid', $uuid)->firstOrFail();

        } catch (\Throwable $th) {

            return $this->generalApiResponse(200, [], 'Brand not found');

        }

        $inputedDataArray = $request->validated();
        $inputedDataArray += ['slug' => str_replace(' ', '-', $request->title)];

        $brand->update($inputedDataArray);

        return $this->generalApiResponse(200, [$brand]);
    }
}
