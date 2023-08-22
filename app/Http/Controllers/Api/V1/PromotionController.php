<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PromotionCollection;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
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
