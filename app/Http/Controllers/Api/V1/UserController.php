<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\V1\UserOrderCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getUserOrders(Request $request)
    {
        $user = $this->guard()->user();

        $query = Order::where('user_id', $user->id);

        if ($request->sortBy) {

            if ($request->desc) {

                $query->orderByDesc($request->sortBy);

            }
            else {

                $query->orderBy($request->sortBy);

            }

        }

        elseif ($request->desc) {

            $query->latest();

        }

        return new UserOrderCollection($query->paginate($request->limit ?? 10));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
