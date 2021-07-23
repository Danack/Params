<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\Messages;
use ParamsTest\Integration\ReviewScore;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalType;
use Params\ProcessedValues;
use Params\InputStorage\ArrayInputStorage;

/**
 * @coversNothing
 */
class GetOptionalTypeTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetOptionalType
     */
    public function testWorks()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'comment' => 'Hello world'];

        $rule = GetOptionalType::fromClass(ReviewScore::class);
        $validationResult = $rule->process(
            $validator, ArrayInputStorage::fromArray($data)
        );

        $this->assertNoErrors($validationResult);

        $item = $validationResult->getValue();
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalType
     */
    public function testWorksWithRules()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'comment' => 'Hello world'];

        $rule = GetOptionalType::fromClassAndRules(
            ReviewScore::class,
            ReviewScore::getInputParameterList()
        );
        $validationResult = $rule->process(
            $validator, ArrayInputStorage::fromArray($data)
        );

        $this->assertNoErrors($validationResult);

        $item = $validationResult->getValue();
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }


    /**
     * @covers \Params\ExtractRule\GetOptionalType
     */
    public function testMissingGivesNull()
    {
        $rule = GetOptionalType::fromClass(ReviewScore::class);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::createMissing('foo')
        );

        $this->assertNoErrors($validationResult);
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalType
     */
    public function testErrors()
    {
        $validator = new ProcessedValues();

        $data = ['score' => 5, 'typo' => 'Hello world'];

        $rule = GetOptionalType::fromClass(ReviewScore::class);
        $validationResult = $rule->process(
            $validator, ArrayInputStorage::fromArray($data)
        );

        $this->assertProblems(
            $validationResult,
            ['/comment' => 'Value not set.']
        );

        $this->assertNull($validationResult->getValue());
    }


    /**
     * @covers \Params\ExtractRule\GetOptionalType
     */
    public function testDescription()
    {
        $rule = GetOptionalType::fromClass(ReviewScore::class);
        $description = $this->applyRuleToDescription($rule);
        $rule->updateParamDescription($description);
        $this->assertFalse($description->getRequired());
        // TODO - need to copy rules from the type class?
    }
}
