<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Sku;

use Params\ExtractRule\GetInt;
use Params\InputToParamInfo;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
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

    public static function getInputToParamInfoList()
    {
        return [
            new InputToParamInfo(
                'sku_id',
                new GetInt()
            ),
        ];
    }
}
