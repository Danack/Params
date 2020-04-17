<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\ExtractRule\GetArrayOfType;
use VarMap\ArrayVarMap;
use ParamsTest\Integration\ReviewScore;
use ParamsTest\Integration\ItemListParams;

require __DIR__ . "/../vendor/autoload.php";


$data = [
    ['foo' => 5, 'bar' => 'Hello world'],
    ['foo' => 6, 'bar' => 'Hello world2']
];

// $varMap = new ArrayVarMap($data);

$items = ReviewScore::createArrayOfTypeFromArray($data);


foreach ($items as $item) {
    echo "Foo: " . $item->getScore() . " bar: " . $item->getComment() . "\n";
}


echo "\nExample behaved as expected.\n";
