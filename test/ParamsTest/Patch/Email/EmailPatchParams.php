<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Email;

use Params\PatchRule\PatchReplace;
use Params\PatchRule\PatchAdd;
use Params\PatchRule\PatchRemove;
use Params\PatchRule\PatchTest;

class EmailPatchParams
{
    public static function getInputParameterList()
    {
        // TODO - allow regexes in paths
        // e.g. $pattern =  '#/projects/(?P<project_name>.+)#iux';

        $rules = [
            new PatchTest(
                '/email/user',
                CheckUserEmailMatches::class,
                CheckUserEmailMatches::getInputParameterList()
            ),
            new PatchTest(
                '/email/admin',
                CheckAdminEmailMatches::class,
                CheckAdminEmailMatches::getInputParameterList()
            ),


//            new PatchReplace('/sku/prices', SkuPriceReplace::class, SkuPriceReplace::getInputParameterList()),
//            new PatchRemove('/sku/prices', SkuPriceRemove::class, SkuPriceRemove::getInputParameterList()),
        ];

        return $rules;
    }
}
