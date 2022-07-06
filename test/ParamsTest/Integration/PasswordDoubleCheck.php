<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\PropertyDefinition;
use Type\SafeAccess;
use Type\ExtractRule\GetString;
use Type\ProcessRule\MinLength;
use Type\ProcessRule\MaxLength;
use Type\Create\CreateOrErrorFromVarMap;
use Type\ProcessRule\DuplicatesParam;
use Type\Type;

class PasswordDoubleCheck implements Type
{
    use SafeAccess;
    use CreateOrErrorFromVarMap;

    /** @var string  */
    private $password;

    /** @var string */
    private $password_repeat;

    public function __construct(string $password, string $password_repeat)
    {
        $this->password = $password;
        $this->password_repeat = $password_repeat;
    }

    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
                'password',
                new GetString(),
                new MinLength(6),
                new MaxLength(60)
            ),
            new PropertyDefinition(
                'password_repeat',
                new GetString(),
                new DuplicatesParam('password')
            ),
        ];
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPasswordRepeat(): string
    {
        return $this->password_repeat;
    }
}
