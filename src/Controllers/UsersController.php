<?php

namespace Lockminds\LaravelAuth\Controllers;

use Illuminate\Http\JsonResponse;
use Lockminds\LaravelAuth\Helpers\Responses;
use Lockminds\LaravelAuth\Models\User;

class UsersController extends BaseController
{
    public function index(): JsonResponse
    {
        try {
            $profile = \DB::table("users")
                ->select(["id", "name", "email"])
                ->paginate();

            return Responses::success(data: $profile);
        } catch (\Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 400);
        }
    }

    public function profile(): JsonResponse
    {
        try {
            $id = auth('api')->id();
            $profile = User::find($id);

            return Responses::success(data: $profile);
        } catch (\Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 400);
        }
    }
}
