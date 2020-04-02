<?php

declare(strict_types = 1);

namespace ParamsTest;

use ParamsTest\Patch\Sku\SkuPatchParams;
use Params\ParamsExecutor;
use ParamsTest\Patch\Sku\SkuPriceAdd;
use ParamsTest\Patch\Sku\SkuPriceReplace;
use ParamsTest\Patch\Sku\SkuPriceRemove;
use function Params\createPatch;

/**
 * @coversNothing
 * @group patchy
 */
class SkuPatchTest extends BaseTestCase
{
    public function testVeryBasic()
    {
        $name = 'Prod name';
        $description = 'A product description.';
        $priceInEur = 10000;
        $priceInGbp = 10100;
        $priceInUsd = 10200;

        $data = [[
            "op" => "add",
            "path" => "/sku_with_price",
            "value" => [
                'name' => $name,
                'description' => $description,
                'price_eur' => $priceInEur,
                'price_gbp' => $priceInGbp,
                'price_usd' => $priceInUsd,
            ]
        ]];

        $operations = createPatch(
            SkuPatchParams::getInputToParamInfoList(),
            json_decode_safe(json_encode($data))
        );

        $this->assertCount(1, $operations);

        /** @var SkuPriceAdd $skuPriceAdd */
        $skuPriceAdd = $operations[0];

        $this->assertInstanceOf(SkuPriceAdd::class, $skuPriceAdd);

        $this->assertSame($name, $skuPriceAdd->getName());
        $this->assertSame($description, $skuPriceAdd->getDescription());

        $this->assertSame($priceInEur, $skuPriceAdd->getPriceEur());
        $this->assertSame($priceInGbp, $skuPriceAdd->getPriceGbp());
        $this->assertSame($priceInUsd, $skuPriceAdd->getPriceUsd());
    }

    public function testMultipleOps()
    {
        $name = 'Prod name';
        $description = 'A product description.';
        $priceInEur = 10000;
        $priceInGbp = 10100;
        $priceInUsd = 10200;

        $replaceId = 51;
        $replaceName = 'Prod name';
        $replaceDescription = 'A product description.';
        $replacePriceInEur = 10000;
        $replacePriceInGbp = 10100;
        $replacePriceInUsd = 10200;

        $data = [
            [
                "op" => "add",
                "path" => "/sku_with_price",
                "value" => [
                    'name' => $name,
                    'description' => $description,
                    'price_eur' => $priceInEur,
                    'price_gbp' => $priceInGbp,
                    'price_usd' => $priceInUsd,
                ]
            ],

            [
                "op" => "replace",
                "path" => "/sku_with_price",
                "value" => [
                    'sku_id' => $replaceId,
                    'name' => $replaceName,
                    'description' => $replaceDescription,
                    'price_eur' => $replacePriceInEur,
                    'price_gbp' => $replacePriceInGbp,
                    'price_usd' => $replacePriceInUsd,
                ]
            ],









            // Path not implemented yet
//            [
//                "op" => "remove",
//                "path" => "/sku_with_price/4",
//            ],
        ];

        $operations = createPatch(
            SkuPatchParams::getInputToParamInfoList(),
            json_decode_safe(json_encode($data))
        );

        $this->assertCount(2, $operations);

        /** @var SkuPriceAdd $skuPriceAdd */
        $skuPriceAdd = $operations[0];

        /** @var SkuPriceReplace $skuPriceReplace */
        $skuPriceReplace = $operations[1];
//        $skuPriceRemove = $operations[2];

        $this->assertInstanceOf(SkuPriceAdd::class, $skuPriceAdd);
        $this->assertInstanceOf(SkuPriceReplace::class, $skuPriceReplace);
//        $this->assertInstanceOf(SkuPriceRemove::class, $skuPriceRemove);

        // asserting $skuPriceAdd
        $this->assertSame($name, $skuPriceAdd->getName());
        $this->assertSame($description, $skuPriceAdd->getDescription());
        $this->assertSame($priceInEur, $skuPriceAdd->getPriceEur());
        $this->assertSame($priceInGbp, $skuPriceAdd->getPriceGbp());
        $this->assertSame($priceInUsd, $skuPriceAdd->getPriceUsd());

        // asserting $skuPriceReplace



        $this->assertSame($replaceId, $skuPriceReplace->getSkuId());
        $this->assertSame($replaceName, $skuPriceReplace->getName());
        $this->assertSame($replaceDescription, $skuPriceReplace->getDescription());
        $this->assertSame($replacePriceInEur, $skuPriceReplace->getPriceEur());
        $this->assertSame($replacePriceInGbp, $skuPriceReplace->getPriceGbp());
        $this->assertSame($replacePriceInUsd, $skuPriceReplace->getPriceUsd());

        // asserting $skuPriceRemove
    }
}
