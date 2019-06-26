<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use VarMap\VarMap;
use Params\FirstRule\GetStringOrDefault;
use Params\FirstRule\GetString;
use Params\SubsequentRule\Enum;
use Params\SafeAccess;
use Params\Create\CreateFromVarMap;

//class EnumExample
//{
//
//    use SafeAccess;
//    use CreateFromVarMap;
//
//    const NAME = 'status';
//
//    const VALUES = [
//        'available',
//        'pending',
//        'sold'
//    ];
//
//    public static function getRules(VarMap $variableMap)
//    {
//        return [
//            self::NAME => [
//                new GetString($variableMap),
//                new Enum(self::VALUES),
//            ],
//        ];
//    }
//}
