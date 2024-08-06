<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use YoungPandas\DataFilter\Facades\Filter;

/**
 * Summary of CreateUserService
 * 
 * This class provides methods to handle the request data, create a new user, and handle the response data. 
 * 
 * Use the Filter facade to filter the request and response data.
 * 
 * It's as simple as calling Filter::filterRequestData($data, $rulesFilePath) and Filter::filterResponseData($data, $rulesFilePath).
 * 
 * Available methods:
 * - hadleDataArray(array $data): array
 * - HandleDataObject(array $data): object
 * - handleRequestData(Request $request): array
 * - handleResponseData(User $newUser): object
 * 
 * Feel free to modify this class as needed.
 * 
 * Hope you enjoy using this package. If you have any questions or need help, please feel free to reach out to us at https://youngpandas.com/contact
 */
class CreateUserService
{
    public function handleRequestData(Request $request): array
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        $sanitized = Filter::filterRequestData($validated, 'createUser.json');
        $data = $sanitized ?? [];
        if (empty($data)) {
            throw new \RuntimeException('Failed to extract example data');
        }
        return $data;
    }

    public function createUser(array $data): User
    {
        if (empty($data) || !is_array($data)) {
            throw new \RuntimeException('Data is empty or not an array');
        }
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password'])) {
            throw new \RuntimeException('Required data is missing');
        }
        if (User::where('email', $data['email'])->exists()) {
            throw new \RuntimeException('User with this email already exists');
        }
        if (empty($data['password'])) {
            $data['password'] = Str::random(12);
        }
        $newUser = User::create([
            "name" => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        if (!$newUser) {
            throw new \RuntimeException('Failed to create a new user');
        }
        return $newUser;
    }

    public function handleResponseData(User $newUser): object
    {
        if (!$newUser || empty($newUser)) {
            throw new \RuntimeException('Example object is empty');
        }
        $response = [
            "status" => "success",
            "message" => "User created successfully.",
            "data" => [
                "name" => $newUser['name'],
                "email" => $newUser['email'],
            ],
        ];
        if (!Filter::filterResponseData($response, 'createUser.json')) {
            throw new \RuntimeException('Failed to filter response data');
        }
        return (object) $response;
    }
}
