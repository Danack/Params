<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\InputTypeSpec;
use TypeSpec\SafeAccess;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\Create\CreateOrErrorFromVarMap;
use TypeSpec\ProcessRule\DuplicatesParam;
use TypeSpec\TypeSpec;

class PasswordDoubleCheck implements TypeSpec
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

    public static function getInputTypeSpecList(): array
    {
        return [
            new InputTypeSpec(
                'password',
                new GetString(),
                new MinLength(6),
                new MaxLength(60)
            ),
            new InputTypeSpec(
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
