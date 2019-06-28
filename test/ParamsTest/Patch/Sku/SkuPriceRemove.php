<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Sku;

use Params\FirstRule\GetInt;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MinIntValue;
use Params\SafeAccess;

class SkuPriceRemove
{
    use SafeAccess;

    /** @var int */
    private $sku_id;

    /**
     * @return int
     */
    public function getSkuId(): int
    {
        return $this->sku_id;
    }


    public static function getRules()
    {
        return [
            'sku_id' => [
                new GetInt()
            ],
        ];
    }
}
