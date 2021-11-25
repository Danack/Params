<?php

declare(strict_types=1);

use ParamsExample\DTOTypes\TestDTO;
use function Params\validate;

require __DIR__ . "/../vendor/autoload.php";


$dto = new TestDTO('red', 5);
[$object, $validationProblems] = validate($dto);
echo "1 - There were " . count($validationProblems) . " validation problems.\n";



$dto = new TestDTO('purple', -15);
[$object, $validationProblems] = validate($dto);
echo "2 - there were " . count($validationProblems) . " validation problems.\n";

foreach ($validationProblems as $validationProblem) {
    /** @var \Params\ValidationProblem $validationProblem */
    echo $validationProblem->getProblemMessage() . "\n";
}


echo "\nExample behaved as expected.\n";
exit(0);
