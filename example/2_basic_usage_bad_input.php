<?php

declare(strict_types=1);

use TypeSpecExample\GetArticlesParams;
use VarMap\ArrayVarMap;
use TypeSpec\Exception\ValidationException;

require __DIR__ . "/../vendor/autoload.php";

$varMap = new ArrayVarMap([]);

try {
    $varMap = new ArrayVarMap(['ordering' => 'not a valid value']);
    $articleGetIndexParams = GetArticlesParams::createFromVarMap($varMap);

    echo "shouldn't reach here.";
    exit(-1);
}
catch (ValidationException $ve) {
    echo "There were validation problems parsing the input:\n  ";
    echo implode("\n  ", $ve->getValidationProblemsAsStrings());

    echo "\nExample behaved as expected.\n";
    exit(0);
}
