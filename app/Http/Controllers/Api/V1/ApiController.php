<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    protected $policyClass;
    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }
        $includeValues = explode(',', strtolower($param));
        return in_array(strtolower($relationship), $includeValues);
    }
    public function isAble($ability, $targetModel)
    {
        try {
            Gate::authorize($ability, [$targetModel, $this->policyClass]);
            return true;
        } catch (AuthenticationException $e) {
            return false;
        }
    }
}
