<?php

namespace Lockminds\LaravelAuth\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Lockminds\LaravelAuth\Helpers\Responses;
use Throwable;

class AuthorizedAccessController extends BaseController
{
    public function destroy(Request $request): JsonResponse
    {

        try {

            $tokenId = $request->user()->token()->id;
            $tokenRepository = app(TokenRepository::class);
            $refreshTokenRepository = app(RefreshTokenRepository::class);

            // Revoke an access token...
            $tokenRepository->revokeAccessToken($tokenId);

            // Revoke all of the token's refresh tokens...
            $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

            return Responses::success(code: 'token_revoked', message: 'Token revoked');

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }
}
