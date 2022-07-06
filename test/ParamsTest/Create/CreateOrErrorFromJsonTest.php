<?php

declare(strict_types = 1);

namespace ParamsTest\Create;

use ParamsTest\BaseTestCase;
use ParamsTest\Integration\IntArrayParams;
use function JsonSafe\json_encode_safe;

/**
 * @coversNothing
 */
class CreateOrErrorFromJsonTest extends BaseTestCase
{
    /**
     * @covers \Type\Create\CreateOrErrorFromJson
     */
    public function testWorks()
    {
        $name = 'John';
        $values = [3, 6, 9, 12];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        $json = json_encode_safe($data);

        [$intArrayParams, $errors] =  IntArrayParams::createOrErrorFromJson($json);

        $this->assertEmpty($errors);
        $this->assertInstanceOf(IntArrayParams::class, $intArrayParams);
        $this->assertSame($name, $intArrayParams->getName());
        $this->assertSame($values, $intArrayParams->getCounts());
    }


    /**
     * @covers \Type\Create\CreateOrErrorFromJson
     */
    public function testErrors()
    {
        $name = 'John';

        $data = [
            'name' => $name,

        ];

        $json = json_encode_safe($data);

        [$intArrayParams, $errors] =  IntArrayParams::createOrErrorFromJson($json);

        $this->assertNull($intArrayParams);


        $this->assertCount(1, $errors);
        $validationProblem = $errors[0];
        $this->assertInstanceOf(\Type\ValidationProblem::class, $validationProblem);
        /** @var $validationProblem \Type\ValidationProblem */

        $this->assertSame(
            \Type\Messages::VALUE_NOT_SET,
            $validationProblem->getProblemMessage()
        );

        $this->assertSame('/counts', $validationProblem->getInputStorage()->getPath());
    }
}
