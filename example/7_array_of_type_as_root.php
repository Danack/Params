<?php

declare(strict_types=1);

namespace TypeSpecExample;

use TypeSpec\ExtractRule\GetArrayOfType;
use VarMap\ArrayVarMap;
use TypeSpecTest\Integration\ReviewScore;
use TypeSpecTest\Integration\ItemListParams;

require __DIR__ . "/../vendor/autoload.php";


$data = [
    ['score' => 5, 'comment' => 'Hello world'],
    ['score' => 6, 'comment' => 'Hello world2']
];

$items = ReviewScore::createArrayOfTypeFromArray($data);

foreach ($items as $item) {
    echo "Score: " . $item->getScore() . " comment: " . $item->getComment() . "\n";
}


echo "\nExample behaved as expected.\n";
