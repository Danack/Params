<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Sku;

use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetString;
use Params\InputToParamInfo;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\SafeAccess;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;

class SkuPriceAdd
{
    use SafeAccess;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var int */
    private $price_eur;

    /** @var int */
    private $price_gbp;

    /** @var int */
    private $price_usd;

    /**
     *
     * @param string $name
     * @param string $description
     * @param int $price_eur
     * @param int $price_gbp
     * @param int $price_usd
     */
    public function __construct(
        string $name,
        string $description,
        int $price_eur,
        int $price_gbp,
        int $price_usd
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->price_eur = $price_eur;
        $this->price_gbp = $price_gbp;
        $this->price_usd = $price_usd;
    }

    public static function getInputToParamInfoList()
    {
        return [
            new InputToParamInfo(
                'name',
                new GetString(),
                new MinLength(8),
                new MaxLength(256)
            ),
            new InputToParamInfo(
                'description',
                new GetString(),
                new MinLength(8),
                new MaxLength(256)
            ),
            new InputToParamInfo(
                'price_eur',
                new GetInt(),
                new MinIntValue(10000),
                new MaxIntValue(1000000)
            ),

            new InputToParamInfo(
                'price_gbp',
                new GetInt(),
                new MinIntValue(10000),
                new MaxIntValue(1000000)
            ),
            new InputToParamInfo(
                'price_usd',
                new GetInt(),
                new MinIntValue(10000),
                new MaxIntValue(1000000)
            ),
        ];
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getPriceEur(): int
    {
        return $this->price_eur;
    }

    /**
     * @return int
     */
    public function getPriceGbp(): int
    {
        return $this->price_gbp;
    }

    /**
     * @return int
     */
    public function getPriceUsd(): int
    {
        return $this->price_usd;
    }
}
