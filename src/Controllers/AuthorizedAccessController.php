<?php

namespace Lockminds\LaravelAuth\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;
use Lockminds\LaravelAuth\Helpers\Responses;
use Throwable;

class AuthorizedAccessController extends AuthorizedAccessTokenController
{
    public function destroy(Request $request, $tokenId): JsonResponse
    {

        try {

            $tokenId = $request->user()->token()->id;

            $token = $this->tokenRepository->findForUser(
                $tokenId, $request->user()->getAuthIdentifier()
            );

            if (is_null($token)) {
                return Responses::badCredentials(code: 'invalid_token', message: 'Invalid token');
            }

            $token->revoke();

            $this->refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

            return Responses::success(code: 'token_revoked', message: 'Token revoked');

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }
}
