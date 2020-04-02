<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\ExtractRule\GetArrayOfType;
use VarMap\ArrayVarMap;
use ParamsTest\Integration\ItemParams;
use ParamsTest\Integration\ItemListParams;

require __DIR__ . "/../vendor/autoload.php";


$data = [
    ['foo' => 5, 'bar' => 'Hello world'],
    ['foo' => 6, 'bar' => 'Hello world2']
];

// $varMap = new ArrayVarMap($data);

$items = ItemParams::createArrayOfTypeFromArray($data);


foreach ($items as $item) {
    echo "Foo: " . $item->getFoo() . " bar: " . $item->getBar() . "\n";
}


echo "\nExample behaved as expected.\n";
