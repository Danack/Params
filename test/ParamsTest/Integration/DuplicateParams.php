<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\FirstRule\GetInt;
use Params\SafeAccess;
use Params\FirstRule\GetString;
use Params\SubsequentRule\MinLength;
use Params\SubsequentRule\MaxLength;
use Params\Create\CreateOrErrorFromVarMap;
use Params\SubsequentRule\DuplicatesParam;

class DuplicateParams
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

    public static function getRules()
    {
        return [
            'password' => [
                new GetString(),
                new MinLength(6),
                new MaxLength(60)
            ],
            'password_repeat' => [
                new GetString(),
                new DuplicatesParam('password'),
            ],
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
