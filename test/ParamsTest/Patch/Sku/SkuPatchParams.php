<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Sku;

use Params\PatchRule\PatchReplace;
use Params\PatchRule\PatchAdd;
use Params\PatchRule\PatchRemove;

class SkuPatchParams
{
    public static function getInputParameterList()
    {
        // TODO - allow regexes in paths
        // e.g. $pattern =  '#/projects/(?P<project_name>.+)#iux';

        $rules = [
            new PatchAdd(
                '/sku/prices',
                SkuPriceAdd::class,
                SkuPriceAdd::getInputParameterList()
            ),
            new PatchReplace(
                '/sku/prices',
                SkuPriceReplace::class,
                SkuPriceReplace::getInputParameterList()
            ),
            new PatchRemove(
                '/sku/prices',
                SkuPriceRemove::class,
                SkuPriceRemove::getInputParameterList()
            ),
        ];

        return $rules;
    }
}
