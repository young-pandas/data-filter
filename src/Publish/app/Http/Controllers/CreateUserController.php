<?php

namespace App\Http\Controllers;

use App\Services\CreateUserService;
use Illuminate\Http\Request;

/**
 * Summary of CreateUserController
 * 
 * It's a simple example to demonstrate the use of services in a Laravel application to keep the controller clean and focused on handling requests and responses.
 * 
 * We use the CreateUserService to handle the request data, create a new user, and handle the response data.
 * @requires App\Services\CreateUserService
 * 
 * Feel free to modify this class as needed. 
 * 
 * Hope you enjoy using this package. If you have any questions or need help, please feel free to reach out to us at https://youngpandas.com/contact
 */
class CreateUserController extends Controller
{
    private CreateUserService $service;

    public function __construct(CreateUserService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        try {
            $sanitized = $this->service->handleRequestData($request);
            $newUser = $this->service->createUser($sanitized);
            $response = $this->service->handleResponseData($newUser);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
