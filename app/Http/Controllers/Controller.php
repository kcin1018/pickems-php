<?php

namespace Pickems\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function fetchData(Request $request)
    {
        // fetch all the data
        $data = [];
        $requestData = $request->all();
        if (isset($requestData['data'])) {
            // load the attributes if exists
            if (isset($requestData['data']['attributes'])) {
                $data = $requestData['data']['attributes'];
            }

            // load the relationships if exists
            if (isset($requestData['data']['relationships'])) {
                foreach ($requestData['data']['relationships'] as $relationship => $relationshipData) {
                    if (isset($relationshipData['data']['id'])) {
                        $data[$relationship.'_id'] = $relationshipData['data']['id'];
                    }
                }
            }
        }

        return $data;
    }

    public function apiValidation($request, $rules)
    {
        // make the validator
        $validator = Validator::make($request->all(), $rules);

        // if the validation fails throw the exception
        if ($validator->fails()) {
            throw new ResourceException('Invalid data', $validator->errors());
        }
    }

    public function apiAuthorize($action, $object)
    {
        if (Gate::denies($action, $object)) {
            throw new ResourceException('Permission Denied', ['You are not able to perform this action'], null, [], 403);
        }
    }
}
