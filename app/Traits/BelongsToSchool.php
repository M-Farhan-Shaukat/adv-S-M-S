<?php

namespace App\Traits;

use App\Models\Scopes\SchoolScope;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool()
    {
        // auto assign school_id
        static::creating(function ($model) {

            if (empty($model->school_id)) {

                if (app()->bound('school')) {
                    $model->school_id = app('school')->id;
                }

                // 🔥 fallback from relation
                elseif (method_exists($model, 'schoolSession') && $model->schoolSession) {
                    $model->school_id = $model->schoolSession->school_id;
                }
            }
        });

        // global scope
        static::addGlobalScope(new SchoolScope);
    }
}
