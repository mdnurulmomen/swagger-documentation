<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostCollection;

class PostController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();

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

        return new PostCollection($query->paginate($request->limit ?? 10));
    }

    public function show($uuid)
    {
        $post = Post::where('uuid', $uuid)->firstOrFail();

        return $this->generalApiResponse(200, [$post]);
    }
}
