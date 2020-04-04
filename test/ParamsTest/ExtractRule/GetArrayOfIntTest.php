<?php

declare(strict_types = 1);

namespace ParamsTest\ExtractRule;

use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfInt;
use VarMap\ArrayVarMap;
use Params\ParamsValuesImpl;
use Params\ProcessRule\MaxIntValue;
use Params\Path;
use Params\DataLocator\StandardDataLocator;
use function Params\createPath;

/**
 * @coversNothing
 */
class GetArrayOfIntTest extends BaseTestCase
{
    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testWorks()
    {
        $data = [5, 6, 7];

        $dataLocator = StandardDataLocator::fromArray($data);

        $rule = new GetArrayOfInt();
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator,
            $dataLocator
        );

        $this->assertFalse($result->isFinalResult());
        $this->assertCount(0, $result->getValidationProblems());
        $this->assertSame($data, $result->getValue());
    }

    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnType()
    {
        $data = [5, 6, 7, 'banana'];


        $rule = new GetArrayOfInt();
        $validator = new ParamsValuesImpl();
        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator,
            StandardDataLocator::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblem(
//            '/[3]', //'items[3]',
            createPath(['index' => 3]),
            'Value must contain only digits.',
            $validationProblems
        );

//        /** @var \Params\ValidationProblem $firstProblem */
//        $firstProblem = $validationProblems[0];
//        $this->assertSame('/items/3', $firstProblem->getPath());
//        $this->assertSame('Value must contain only digits.', $firstProblem->getProblemMessage());
    }

    /**
     * @covers  \Params\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnSubsequentRule()
    {
        $data = [5, 6, 7, 5001];

        $rule = new GetArrayOfInt(
            new MaxIntValue(20)
        );
        $validator = new ParamsValuesImpl();

        $result = $rule->process(
            Path::fromName('items'),
            new ArrayVarMap($data),
            $validator,
            StandardDataLocator::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $problemMessages = $result->getValidationProblems();

        $this->assertValidationProblem(
            createPath(['index'=> 3]),
            'Value too large. Max allowed is 20',
            $problemMessages
        );
    }
}
