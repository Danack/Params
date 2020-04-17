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
                // This is the path to the patch path
                '/sku/prices',
                SkuPriceAdd::class,
                SkuPriceAdd::getPatchInputParameterList()
            ),
            new PatchReplace(
                '/sku/prices',
                SkuPriceReplace::class,
                SkuPriceReplace::getPatchInputParameterList()
            ),
            new PatchRemove(
                '/sku/prices',
                SkuPriceRemove::class,
                SkuPriceRemove::getPatchInputParameterList()
            ),
        ];

        return $rules;
    }
}
