<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function createdResponse($result, $title = null, $message = null)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'messageTitle' => $title ? $title : 'Created!',
            'message' => $message ? $message : 'Resource created successfully',
        ];
        return response()->json($response, 201);
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $title = null, $message = null)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'messageTitle' => $title ? $title : 'Successfully!',
            'message' => $message ? $message : 'Fetching the data successfully',
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($title = null, $error = null, $errorMessages = [], $code = 500)
    {
    	$response = [
            'success' => false,
            'messageTitle' => $title ? $title : 'Error!',
            'message' => $error ? $error : 'Internal server error',
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

}
