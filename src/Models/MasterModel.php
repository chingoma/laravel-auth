<?php

namespace Lockminds\LaravelAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Lockminds\LaravelAuth\Traits\UuidForKey;
use OwenIt\Auditing\Contracts\Auditable;

abstract class MasterModel extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use UuidForKey;
}
