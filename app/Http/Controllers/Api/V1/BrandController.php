<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BrandRequest;

class BrandController extends Controller
{
    use ApiResponser;

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
    public function storeBrand(BrandRequest $request)
    {
        $inputedDataArray = $request->validated();
        $inputedDataArray += ['slug' => str_replace(' ', '-', $request->title)];

        $newBrand = Brand::create($inputedDataArray);

        return $this->generalApiResponse(200, ['uuid' => $newBrand->uuid]);
    }
}
