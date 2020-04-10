<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetArrayOfType;
use ParamsTest\Integration\ReviewScore;
use VarMap\ArrayVarMap;
use Params\ProcessedValuesImpl;
use ParamsTest\Integration\SingleIntParams;
use Params\Path;
use Params\DataLocator\DataStorage;
use function Params\createPath;

/**
 * @coversNothing
 * @group wip
 */
class GetArrayOfTypeTest extends BaseTestCase
{

    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     * @group debug
     */
    public function testWorksForComplexType()
    {
        $niceComment = "This is great.";
        $badComment = "Not so good.";

        $data = [
            ['score' => 5, 'comment' => $niceComment],
            ['score' => 2, 'comment' => $badComment],
        ];

        $rule = new GetArrayOfType(ReviewScore::class);
        $processedValues = new ProcessedValuesImpl();
        $result = $rule->process(
            $processedValues,
            DataStorage::fromArray($data)
        );

        $this->assertNoValidationProblems($result->getValidationProblems());
        $this->assertFalse($result->isFinalResult());

        $this->assertCount(2, $result->getValue());

        $item1 = ($result->getValue())[0];
        $this->assertInstanceOf(ReviewScore::class, $item1);
        /** @var ReviewScore $item1 */
        $this->assertSame(5, $item1->getScore());
        $this->assertSame($niceComment, $item1->getComment());


        $item2 = ($result->getValue())[1];
        $this->assertInstanceOf(ReviewScore::class, $item2);
        /** @var ReviewScore $item2 */
        $this->assertSame(2, $item2->getScore());
        $this->assertSame($badComment, $item2->getComment());
    }


    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testWorksForIntType()
    {
        $data = [
            ['limit' => 5]
        ];

        $rule = new GetArrayOfType(SingleIntParams::class);
        $validator = new ProcessedValuesImpl();
        $result = $rule->process(
            $validator, DataStorage::fromArray($data)
        );

        $this->assertFalse($result->isFinalResult());

        $this->assertCount(1, $result->getValue());
        $item = ($result->getValue())[0];
        $this->assertInstanceOf(SingleIntParams::class, $item);

        $this->assertNoValidationProblems($result->getValidationProblems());
        /** @var SingleIntParams $item */
        $this->assertSame(5, $item->getLimit());
    }


    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testMissingArrayErrors()
    {
        $this->markTestSkipped("Need to check this is doing the right thing.");
        return;

        $data = [];

        $rule = new GetArrayOfType(ReviewScore::class);
        $validator = new ProcessedValuesImpl();

        $result = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($data)
        );
        $this->assertTrue($result->isFinalResult());
//        $expectedKey = '/items';

        $problems = $result->getValidationProblems();
        $this->assertCount(1, $problems);
        $firstProblem = $problems[0];

        $this->assertEquals("Value must be set.", $firstProblem->getProblemMessage());
        $this->assertNull($result->getValue());
    }


    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testScalarInsteadOfArrayErrors()
    {
        $this->markTestSkipped("Needs fixing.");
        return;

        $data = [
            'items' => 'a banana'
        ];

        $rule = new GetArrayOfType(ReviewScore::class);
        $validator = new ProcessedValuesImpl();
        $result = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($data)
        );
        $this->assertTrue($result->isFinalResult());
        $expectedKey = 'items';

        $problems = $result->getValidationProblems();

        $this->assertCount(1, $problems);
        $this->assertValidationProblem(
            $expectedKey,
            "Value must be an array.",
            $problems
        );

        $this->assertNull($result->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testScalarInsteadOfEntryArrayErrors()
    {
        $this->markTestSkipped("Needs fixing.");
        return;


        $data = [
            // wrong - should be ['limit' => 5]
            5
        ];

        $rule = new GetArrayOfType(SingleIntParams::class);

        $validator = new ProcessedValuesImpl();
        $result = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($data)
        );
        $this->assertTrue($result->isFinalResult());
        $validationProblems = $result->getValidationProblems();
        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblemRegexp(
            'items',
            Messages::ERROR_MESSAGE_ITEM_NOT_ARRAY,
            $validationProblems
        );

        $this->assertNull($result->getValue());
    }



    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testSingleError()
    {
        $this->markTestSkipped("Needs fixing.");
        return;


        $data = [
            'items' => [
                ['foo' => 5, 'bar' => false]
            ],
        ];

        $rule = new GetArrayOfType(ReviewScore::class);
        $validator = new ProcessedValuesImpl();
        $result = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue($data)
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $this->assertCount(1, $result->getValidationProblems());

//        $this->markTestSkipped("This needs fixing");

        $this->assertValidationProblem(
            'items[0]/bar',
            "String too short, min chars is 4",
            $result->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ExtractRule\GetArrayOfType
     */
    public function testMultipleErrors()
    {
        $data = [
            ['score' => 5, 'comment' => 'foo'],
            ['score' => 101, 'comment' => 'world']
        ];

        $validator = new ProcessedValuesImpl();
        $rule = new GetArrayOfType(ReviewScore::class);
        $result = $rule->process(
            $validator, DataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());

        $validationProblems = $result->getValidationProblems();
        $this->assertCount(2, $validationProblems);

        $this->assertValidationProblem(
            createPath(['index' => 0, 'name' => 'comment']),
            "String too short, min chars is 4",
            $validationProblems
        );

        $this->assertValidationProblem(
            createPath(['index' => 1, 'name' => 'score']),
            "Value too large. Max allowed is 100",
            $validationProblems
        );
    }
}
