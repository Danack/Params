<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\InputParameter;
use Params\SafeAccess;
use Params\ExtractRule\GetString;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;
use Params\Create\CreateOrErrorFromVarMap;
use Params\ProcessRule\DuplicatesParam;
use Params\InputParameterList;

class PasswordDoubleCheck implements InputParameterList
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

    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
                'password',
                new GetString(),
                new MinLength(6),
                new MaxLength(60)
            ),
            new InputParameter(
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
