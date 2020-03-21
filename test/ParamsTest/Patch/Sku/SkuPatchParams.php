<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Sku;

use Params\PatchRule\PatchReplace;
use Params\PatchRule\PatchAdd;
use Params\PatchRule\PatchRemove;

class SkuPatchParams
{
    public static function getInputToParamInfoList()
    {
        // TODO - allow regexes in paths
        // e.g. $pattern =  '#/projects/(?P<project_name>.+)#iux';

        $rules = [
            new PatchAdd(
                '/sku/prices',
                SkuPriceAdd::class,
                SkuPriceAdd::getInputToParamInfoList()
            ),
            new PatchReplace(
                '/sku/prices',
                SkuPriceReplace::class,
                SkuPriceReplace::getInputToParamInfoList()
            ),
            new PatchRemove(
                '/sku/prices',
                SkuPriceRemove::class,
                SkuPriceRemove::getInputToParamInfoList()
            ),
        ];

        return $rules;
    }
}
