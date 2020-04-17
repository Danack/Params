<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Email;

use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetString;
use Params\InputParameter;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\SafeAccess;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;
use Params\PatchParameter;
use Params\ScalarPatchInput;

class CheckUserEmailMatches implements \Params\PatchInputParameterList
{
    use SafeAccess;

    /** @var string */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function getInputParameterList()
    {
        return [
            new InputParameter(
                'email',
                new GetString()
            ),
        ];
    }

    /**
     * @return \Params\PatchInputParameter[]
     */
    public static function getPatchInputParameterList()
    {
        return [
            new ScalarPatchInput(
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
