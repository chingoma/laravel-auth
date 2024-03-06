<?php

namespace Lockminds\LaravelAuth;

use Laravel\Passport\Bridge\AccessToken;
use Lockminds\LaravelAuth\Traits\CustomClaimsAccessTokenTrait;

class LaravelAuthAccessToken extends AccessToken
{
    use CustomClaimsAccessTokenTrait;
}
