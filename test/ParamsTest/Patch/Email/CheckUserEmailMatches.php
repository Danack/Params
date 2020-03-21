<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Email;

use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetString;
use Params\InputToParamInfo;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\SafeAccess;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;

class CheckUserEmailMatches
{
    use SafeAccess;

    /** @var string */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function getInputToParamInfoList()
    {
        return [
            new InputToParamInfo(
                'email',
                new GetString()
            ),
        ];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
