<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

use TypeSpec\Create\CreateFromRequest;
use TypeSpec\TypeSpec;
use TypeSpec\InputTypeSpec;

/**
 * This represents the input data
 */
class UserCreateParams implements TypeSpec
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
     * @return \TypeSpec\InputTypeSpec[]
     */
    public static function getInputTypeSpecList(): array
    {
        return [
            new InputTypeSpec(
                'username',
                new GetString(),
                new MinLength(4),
                new MaxLength(2048)
            ),
            new InputTypeSpec(
                'password',
                new GetString(),
                new MinLength(4),
                new MaxLength(256)
            ),
        ];
    }
}
