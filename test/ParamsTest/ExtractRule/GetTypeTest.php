<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use ParamsTest\Integration\ReviewScore;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetType;
use Params\ProcessedValuesImpl;
use Params\Path;
use Params\DataLocator\DataStorage;

/**
 * @coversNothing
 * @group wip
 */
class GetTypeTest extends BaseTestCase
{
//    /**
//     * @covers \Params\ExtractRule\GetString
//     */
//    public function testMissingGivesError()
//    {
//        $rule = new GetInt();
//        $validator = new ParamsValuesImpl();
//        $validationResult = $rule->process(
//            Path::fromName('foo'),
//            new ArrayVarMap([]),
//            $validator
//        );
//        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
//    }

//    public function provideTestWorksCases()
//    {
//        return [
//            ['5', 5],
//            [5, 5],
//        ];
//    }

    /**
     * @covers \Params\ExtractRule\GetType
     */
    public function testWorks()
    {
        $validator = new ProcessedValuesImpl();

        $data = ['score' => 5, 'comment' => 'Hello world'];

        $rule = GetType::fromClass(ReviewScore::class);
        $validationResult = $rule->process(
            $validator, DataStorage::fromArray($data)
        );

        $this->assertNoErrors($validationResult);

        $item = $validationResult->getValue();
        $this->assertInstanceOf(ReviewScore::class, $item);
        /** @var ReviewScore $item */
        $this->assertSame(5, $item->getScore());
        $this->assertSame('Hello world', $item->getComment());
    }


//    public function provideTestErrorCases()
//    {
//        return [
//            [['foo', null]],
//            [['foo', '']],
//            [['foo', '6 apples']],
//            [['foo', 'banana']],
//        ];
//    }
//
//    /**
//     * @covers \Params\ExtractRule\GetInt
//     * @dataProvider provideTestErrorCases
//     */
//    public function testErrors($variables)
//    {
//        $variableName = 'foo';
//
//        $rule = new GetInt();
//        $validator = new ParamsValuesImpl();
//        $validationResult = $rule->process(
//            Path::fromName($variableName),
//            new ArrayVarMap($variables),
//            $validator
//        );
//
//        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
//    }
}
