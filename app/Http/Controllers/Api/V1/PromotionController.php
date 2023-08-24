<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PromotionCollection;

class PromotionController extends Controller
{
    /**
     *
     * @OA\Get(
     *     path="/api/v1/main/promotions",
     *     tags={"Main-Page"},
     *     summary="List all promotions",
     *     operationId="getPromotionList",
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
    public function getPromotionList(Request $request)
    {
        $query = Promotion::query();

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

        return new PromotionCollection($query->paginate($request->limit ?? 10));
    }
}
