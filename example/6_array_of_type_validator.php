<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\ExtractRule\GetArrayOfType;
use VarMap\ArrayVarMap;
use ParamsTest\Integration\ItemParams;
use ParamsTest\Integration\ItemListParams;

require __DIR__ . "/../vendor/autoload.php";



$varMap = new ArrayVarMap([
    'items' => [
        ['foo' => 5, 'bar' => 'Hello world'],
        ['foo' => 6, 'bar' => 'Hello world2']
    ],
    'description' => 'This is some test data.'
]);

$itemList = ItemListParams::createFromVarMap($varMap);

echo "Description: " . $itemList->getDescription() . "\n";

foreach ($itemList->getItems() as $item) {
    echo "Foo: " . $item->getFoo() . " bar: " . $item->getBar() . "\n";
}

echo "\nExample behaved as expected.\n";
