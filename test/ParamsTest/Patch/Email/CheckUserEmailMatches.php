<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Email;

use Params\FirstRule\GetInt;
use Params\FirstRule\GetString;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MinIntValue;
use Params\SafeAccess;
use Params\SubsequentRule\MinLength;
use Params\SubsequentRule\MaxLength;

class CheckUserEmailMatches
{
    use SafeAccess;

    /** @var string */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function getRules()
    {
       return [
            'email' => [
                new GetString()
            ],
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
