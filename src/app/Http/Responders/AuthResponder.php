<?php declare(strict_types=1);

namespace App\Http\Responders;

use App\Models\User;
use Illuminate\Http\Response as Status;
use App\Http\Resources\UserResource;
use App\Domains\Account\CheckMe\CheckMeError;
use App\Domains\Account\Login\LoginError;
use App\Domains\Account\Logout\LogoutError;
use App\Http\Responses\ApiResponse;
use App\Shared\Result;

final class AuthResponder
{
    /**
     * @phpstan-param Result<string, LoginError> $result
     *
     * @phpstan-return ApiResponse
     */
    public function login(Result $result): ApiResponse
    {
        /** @phpstan-var ApiResponse */
        return $result->match(
            onSuccess: fn (string $token) => new ApiResponse(
                data: [
                    'message' => __(key: 'auth.login_successful'),
                    'token' => $token,
                ],
                status: Status::HTTP_OK
            ),
            onError: fn (LoginError $error) => new ApiResponse(
                data: ['message' => __(key: $error->message())],
                status: $error->status()
            )
        );
    }

    /**
     * @phpstan-param Result<bool, LogoutError> $result
     * @phpstan-return ApiResponse
     */
    public function logout(Result $result): ApiResponse
    {
        /** @phpstan-var ApiResponse */
        return $result->match(
            onSuccess: fn (bool $logout) => new ApiResponse(
                data: [
                    'message' => __(key: 'auth.logout_successful'),
                ],
                status: Status::HTTP_OK
            ),
            onError: fn(LogoutError $error) => new ApiResponse(
                data: ['message' => __(key: $error->message())],
                status: $error->status()
            )
        );
    }

    /**
     * @phpstan-param Result<User, CheckMeError> $result
     * @phpstan-return ApiResponse
     */
    public function checkMe(Result $result): ApiResponse
    {
        /** @phpstan-var ApiResponse */
        return $result->match(
            onSuccess: fn(User $user) => new ApiResponse(
                data: new UserResource(resource: $user),
                status: Status::HTTP_OK
            ),
            onError: fn(CheckMeError $error) => new ApiResponse(
                data: ['message' => __(key: $error->message())],
                status: $error->status()
            )
        );
    }
}