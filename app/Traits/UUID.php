<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait UUID
{
    protected static function boot(): void
    {
        parent::boot();

        /**
         * Listen for the creating event on the model.
         * Sets the UUID using Str::uuid() on the instance being created
         */
        static::creating(fn (Model $model) =>
            $model->uuid = Str::uuid(),
        );
    }

    // Tells the database not to auto-increment this field
    public function getIncrementing ()
    {
        return false;
    }

    // Helps the application specify the field type in the database
    public function getKeyType ()
    {
        return 'string';
    }
}
