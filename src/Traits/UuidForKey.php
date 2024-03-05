<?php

namespace Lockminds\LaravelAuth\Traits;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait UuidForKey
{
    /**
     * The "booting" method of the model.
     */
    public static function bootUuidForKey(): void
    {
        static::retrieved(function (Model $model) {
            $model->incrementing = false;  // this is used after instance is loaded from DB
        });

        static::creating(function (Model $model) {
            $model->incrementing = false; // this is used for new instances

            if (empty($model->{$model->getKeyName()})) { // if it's not empty, then we want to use a specific id
                $model->{$model->getKeyName()} = (string) Uuid::uuid7();
            }
        });
    }

    public function initializeUuidForKey(): void
    {
        $this->keyType = 'string';
    }
}
