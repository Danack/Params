<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Email;

use Params\PatchRule\PatchReplace;
use Params\PatchRule\PatchAdd;
use Params\PatchRule\PatchRemove;
use Params\PatchRule\PatchTest;

class EmailPatchParams
{
    public static function getRules()
    {
        // TODO - allow regexes in paths
        // e.g. $pattern =  '#/projects/(?P<project_name>.+)#iux';

        $rules = [
            new PatchTest(
                '/email/user',
                CheckUserEmailMatches::class,
                CheckUserEmailMatches::getRules()
            ),
            new PatchTest(
                '/email/admin',
                CheckAdminEmailMatches::class,
                CheckAdminEmailMatches::getRules()
            ),


//            new PatchReplace('/sku/prices', SkuPriceReplace::class, SkuPriceReplace::getRules()),
//            new PatchRemove('/sku/prices', SkuPriceRemove::class, SkuPriceRemove::getRules()),
        ];

        return $rules;
    }
}
