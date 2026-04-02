<?php

use App\Models\School;

if (!function_exists('school')) {
    function school()
    {
        return app()->bound('school') ? app('school') : null;
    }
}
