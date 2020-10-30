<?php

declare(strict_types = 1);


namespace SeparatingInputTypeFromStoredType;

interface UserRepo
{
    public function createUser(UserCreateParams $userCreateParams): User;
}