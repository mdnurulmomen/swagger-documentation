<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser{

    protected function generalApiResponse($code = 200, $data = [], $error = null, $errors = [])
	{
        return response()->json([
            'success' => $code===200 ? true : false,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
            "extra" => []
        ], $code);
	}

}
