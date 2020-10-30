<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

class User
{
    public function __construct(
        private string $id,
        private string $username,
        private string $password_hash
    ) { }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPasswordHash() {
        return $this->password_hash;
    }
}
