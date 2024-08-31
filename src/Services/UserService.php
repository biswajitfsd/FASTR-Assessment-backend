<?php

namespace App\Services;

class UserService
{
    public function createUserName($user): string
    {
        $user_name = strtolower(substr($user["last_name"], 0, 3));
        $user_name .= strtolower(substr($user["first_name"], 0, 1));
        list(, $domain) = explode("@", $user["email"]);
        $user_name .= "@" . $domain;
        return $user_name;
    }
}