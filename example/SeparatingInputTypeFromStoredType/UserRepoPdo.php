<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

class UserRepoPdo implements UserRepo
{

    public function createUser(UserCreateParams $userCreateParams): User
    {
        $password_hash = generate_password_hash($userCreateParams->getPassword());

        $sql = <<< SQL
insert into
    users
(username, password_hash)
values (:username, :password_hash)  
SQL;

        $params = [
            ':username' => $userCreateParams->getUsername(),
            ':password_hash' => $password_hash
        ];

        $userId = $this->db->insert($sql, $params);

        return new User(
            $userId,
            $userCreateParams->getUsername(),
            $password_hash
        );
    }
}
