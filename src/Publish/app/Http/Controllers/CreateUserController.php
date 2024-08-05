<?php

namespace App\Http\Controllers;

use App\Services\CreateUserService;
use Illuminate\Http\Request;

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
