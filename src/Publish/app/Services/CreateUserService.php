<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * This is an example service class that creates a new user.
 * With this class, you can keep your controllers clean and maintainable.
 * It filters data and performs other operations on the data as per rules defined in the json files.
 */
class CreateUserService extends Service
{
    public function __construct()
    {
        parent::__construct();
    }

    public function handleRequestData(Request $request): array
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        $sanitized = $this->filterRequestData($validated, 'createUser.json');
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
        if (!$this->filterResponseData($response, 'createUser.json')) {
            throw new \RuntimeException('Failed to filter response data');
        }
        return (object) $response;
    }
}
