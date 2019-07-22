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

class CheckAdminEmailMatches
{
    use SafeAccess;

    /** @var string */
    private $email;

    /**
     *
     * @param string $name
     * @param string $description
     * @param int $price_eur
     * @param int $price_gbp
     * @param int $price_usd
     */
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
}
