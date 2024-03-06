<?php

namespace Lockminds\LaravelAuth\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lockminds\LaravelAuth\Helpers\Responses;
use Throwable;

class AuthorizedAccessController extends BaseController
{
    public function destroy(Request $request): JsonResponse
    {

        try {
            $token = $request->bearerToken();
            $tokenId = (new Parser(new JoseEncoder()))->parse($token)->claims()->get('jti');
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
