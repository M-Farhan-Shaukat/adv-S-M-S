<?php

namespace App\Traits;

use App\Models\Scopes\SchoolScope;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool()
    {
        // auto assign school_id
        static::creating(function ($model) {
            if (school() && empty($model->school_id)) {
                $model->school_id = school()->id;
            }
        });

        // global scope
        static::addGlobalScope(new SchoolScope);
    }
}
