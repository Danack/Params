<?php

declare(strict_types = 1);

namespace SeparatingInputTypeFromStoredType;

use Params\Create\CreateFromRequest;
use Params\InputParameterList;
use Params\InputParameter;

/**
 * This represents the input data
 */
class UserCreateParams implements InputParameterList
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
     * @return \Params\InputParameter[]
     */
    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
                'username',
                new GetString(),
                new MinLength(4),
                new MaxLength(2048)
            ),
            new InputParameter(
                'password',
                new GetString(),
                new MinLength(4),
                new MaxLength(256)
            ),
        ];
    }
}
