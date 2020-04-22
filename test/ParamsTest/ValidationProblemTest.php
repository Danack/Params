<?php

declare(strict_types = 1);

namespace ParamsTest;

use Params\ValidationProblem;
use Params\DataLocator\DataStorage;

/**
 * @coversNothing
 */
class ValidationProblemTest extends BaseTestCase
{
    /**
     * @covers \Params\ValidationProblem
     */
    public function testWorks()
    {
        $dataStorage = DataStorage::fromArray([]);

        $key = 'nonexistent';

        $dataStorage = $dataStorage->moveKey($key);
        $problemMessage = 'There was problem';

        $validationProblem = new ValidationProblem($dataStorage, $problemMessage);

        $this->assertSame($problemMessage, $validationProblem->getProblemMessage());
        $this->assertSame($dataStorage, $validationProblem->getDataLocator());
        $this->assertSame('/' . $key . ' ' . $problemMessage, $validationProblem->toString());
    }
}
