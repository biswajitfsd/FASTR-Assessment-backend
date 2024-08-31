<?php

namespace App\Controllers;

use App\Core\Validator;
use App\Services\UserService;

class UserController
{
    protected Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    public function store(): array
    {
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON data'];
        }
        $rules = [
            'first_name' => 'required|string|min##1',
            'last_name' => 'required|string|min##3',
            'email' => 'required|email'
        ];

        if (!$this->validator->validate($data, $rules)) {
            return ['errors' => $this->validator->getErrors()];
        }
        $user_service = new UserService();
        $username = $user_service->createUserName($data);
        return ["username" => $username, "email" => $data["email"]];
    }
}