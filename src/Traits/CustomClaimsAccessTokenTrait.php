<?php

namespace Lockminds\LaravelAuth\Traits;

use Lcobucci\JWT\UnencryptedToken;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use Lockminds\LaravelAuth\Models\Otp;

trait CustomClaimsAccessTokenTrait
{
    use AccessTokenTrait;

    /**
     * Generate a JWT from the access token
     */
    private function convertToJWT(): UnencryptedToken
    {
        $this->initJwtConfiguration();
        $otp = Otp::where("user_id",$this->getUserIdentifier())
            ->where("status","valid")
            ->first();

        return $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new \DateTimeImmutable())
            ->canOnlyBeUsedAfter(new \DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes())
            ->withClaim('otp', $otp->status??"") // your custom claim
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }

    /**
     * Generate a string representation from the access token
     */
    public function __toString()
    {
        return $this->convertToJWT()->toString();
    }
}
