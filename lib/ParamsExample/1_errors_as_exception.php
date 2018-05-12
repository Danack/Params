<?php

declare(strict_types=1);

use ParamsExample\ArticleGetIndexParams;
use VarMap\ArrayVarMap;
use Params\Exception\ValidationException;

require __DIR__ . "/../../vendor/autoload.php";

$varMap = new ArrayVarMap([]);

$articleGetIndexParams = ArticleGetIndexParams::fromVarMap($varMap);

echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
echo "Ordering: " . var_export($articleGetIndexParams->getOrdering()->toOrderArray(), true) . PHP_EOL;

try {
    $varMap = new ArrayVarMap(['order' => 'error']);
    [$articleGetIndexParams, $errors] = ArticleGetIndexParams::fromVarMap($varMap);

    if (count($errors) !== 0) {
        echo "There were errors creating ArticleGetIndexParams from input\n  " . implode('\n  ', $errors);
    }
    else {
        echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
        echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
        echo "Ordering: " . var_export($articleGetIndexParams->getOrdering()->toOrderArray(), true) . PHP_EOL;
    }

    echo "shouldn't reach here.";
    exit(-1);
}
catch (ValidationException $ve) {
    echo "There were validation problems parsing the input:\n  ";
    echo implode("\n  ", $ve->getValidationProblems());

    echo "\nExample behaved as expected.\n";
    exit(0);
}
