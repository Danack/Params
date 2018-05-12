<?php

declare(strict_types=1);

use ParamsExample\ArticleGetIndexParams;
use VarMap\ArrayVarMap;

require __DIR__ . "/../../vendor/autoload.php";

$varmap = new ArrayVarMap([]);

[$articleGetIndexParams, $errors] = ArticleGetIndexParams::fromMagic($varmap);

echo "After Id: " . $articleGetIndexParams->getAfterId() . PHP_EOL;
echo "Limit:    " . $articleGetIndexParams->getLimit() . PHP_EOL;
echo "Ordering: " . var_export($articleGetIndexParams->getOrdering()->toOrderArray(), true) . PHP_EOL;

// Handle errors
$varmap = new ArrayVarMap(['order' => 'error']);
[$articleGetIndexParams, $errors] = ArticleGetIndexParams::fromMagic($varmap);

if (count($errors) !== 0) {
    echo "There were errors creating ArticleGetIndexParams from input\n  " . implode('\n  ', $errors);
    echo "\nExample behaved as expected.\n";
    exit(0);
}

echo "shouldn't reach here.";
exit(-1);
