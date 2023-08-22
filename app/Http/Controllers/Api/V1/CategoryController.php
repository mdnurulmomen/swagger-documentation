<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryCollection;

class CategoryController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
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

    public function show($uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();

        return $this->generalApiResponse(200, [$category]);
    }
}
