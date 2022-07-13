<?php

declare(strict_types = 1);

namespace TypeSpecTest\Create;

use TypeSpecTest\BaseTestCase;
use TypeSpecTest\Integration\IntArrayParams;
use function JsonSafe\json_encode_safe;

/**
 * @coversNothing
 */
class CreateOrErrorFromJsonTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Create\CreateOrErrorFromJson
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
     * @covers \TypeSpec\Create\CreateOrErrorFromJson
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
        $this->assertInstanceOf(\TypeSpec\ValidationProblem::class, $validationProblem);
        /** @var $validationProblem \TypeSpec\ValidationProblem */

        $this->assertSame(
            \TypeSpec\Messages::VALUE_NOT_SET,
            $validationProblem->getProblemMessage()
        );

        $this->assertSame('/counts', $validationProblem->getInputStorage()->getPath());
    }
}
