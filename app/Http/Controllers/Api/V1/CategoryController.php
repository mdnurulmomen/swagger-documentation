<?php

namespace App\Http\Controllers\API\V1;

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

    public function store(Request $request)
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


    public function show($uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();

        return $this->generalApiResponse(200, [$category]);
    }
}
