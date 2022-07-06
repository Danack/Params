<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

use Type\Create\CreateFromRequest;
use Type\Type;
use Type\PropertyDefinition;

/**
 * This represents the input data
 */
class UserCreateParams implements Type
{
    use ToArray;
    use CreateFromRequest;

    public function __construct(
        private string $username,
        private string $password
    ) { }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return \Type\PropertyDefinition[]
     */
    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
                'username',
                new GetString(),
                new MinLength(4),
                new MaxLength(2048)
            ),
            new PropertyDefinition(
                'password',
                new GetString(),
                new MinLength(4),
                new MaxLength(256)
            ),
        ];
    }
}
