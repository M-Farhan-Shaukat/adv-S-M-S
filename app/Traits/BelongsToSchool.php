<?php
namespace App\Traits;

use App\Models\Scopes\SchoolScope;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool()
    {
        static::creating(function ($model) {

            // ✅ 1. from app container
            if (empty($model->school_id) && app()->bound('school')) {
                $model->school_id = app('school')->id;
            }

            // ✅ 2. fallback from session_id
            elseif (
                empty($model->school_id) &&
                method_exists($model, 'schoolSession') &&
                !empty($model->school_session_id)
            ) {
                $session = \App\Models\SchoolSession::find($model->school_session_id);

                if ($session) {
                    $model->school_id = $session->school_id;
                }
            }
        });

        static::addGlobalScope(new SchoolScope);
    }
}
