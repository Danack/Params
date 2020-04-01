<?php

declare(strict_types=1);

namespace ParamsExample;

require __DIR__ . "/../vendor/autoload.php";



$correctData = [
    'name' => 'Dan',
    'mac_address' => 'a1:b2:c3:d4:e5:f6'
];

/** @var ComputerDetailsParams $computerDetails */
[$computerDetails, $validationErrors] =
    ComputerDetailsParams::createOrErrorFromArray($correctData);

printf(
    "Correct data\n\tName: [%s]\tMac address [%s]\n",
    $computerDetails->getName(),
    $computerDetails->getMacAddress()
);


$badData = [
    'name' => 'Dan',
    'mac_address' => 'a1:b2:c3:d4:e5:banana'
];

/** @var \Params\ValidationProblem[] $validationErrors */
[$computerDetails, $validationErrors] =
    ComputerDetailsParams::createOrErrorFromArray($badData);

if (count($validationErrors) === 0) {
    echo "";
    echo "shouldn't reach here.";
    exit(-1);
}

echo "Bad data correctly detected: \n";

foreach ($validationErrors as $validationError) {
    /** @var \Params\ValidationProblem $validationError */
    echo "\t" . $validationError->getProblemMessage() . "\n";
}


echo "\nExample behaved as expected.\n";
