<?php

declare(strict_types = 1);

namespace ParamsTest;

use Params\ValidationProblem;
use Params\InputStorage\ArrayInputStorage;

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
        $dataStorage = ArrayInputStorage::fromArray([]);

        $key = 'nonexistent';

        $dataStorage = $dataStorage->moveKey($key);
        $problemMessage = 'There was problem';

        $validationProblem = new ValidationProblem($dataStorage, $problemMessage);

        $this->assertSame($problemMessage, $validationProblem->getProblemMessage());
        $this->assertSame($dataStorage, $validationProblem->getInputStorage());
        $this->assertSame('/' . $key . ' ' . $problemMessage, $validationProblem->toString());
    }
}
