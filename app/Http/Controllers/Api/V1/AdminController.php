<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\UserCollection;

class AdminController extends Controller
{
    use ApiResponser;

    /**
     * Display a listing of the resource.
     */
    public function getUserList(Request $request)
    {
        $query = User::query();

        if ($request->first_name) {

            $query->where('first_name', 'like', "$request->first_name%");

        }

        if ($request->email) {

            $query->where('email', 'like', "$request->email%");

        }

        if ($request->phone) {

            $query->where('phone_number', 'like', "$request->phone%");

        }

        if ($request->address) {

            $query->where('address', 'like', "$request->address%");

        }

        if ($request->created_at) {

            $query->whereDate('created_at', date('Y-m-d', strtotime($request->created_at)));

        }

        if (filter_var($request->marketing, FILTER_VALIDATE_BOOLEAN)) {

            $query->where('is_marketing', $request->marketing);

        }

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

        return new UserCollection($query->paginate($request->limit ?? 10));
    }

    public function updateUser($uuid, Request $request)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'required|string|min:8|max:255|confirmed',
            'avatar' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'is_marketing' => 'nullable|boolean'
        ]);

        if($validator->fails()){
            return $this->generalApiResponse(422, [], null, $validator->messages());
        }

        $user->update($validator->validated());

        return $this->generalApiResponse(200, [$user]);
    }

    public function deleteUser($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $user->delete();

        return $this->generalApiResponse(200);
    }
}
