<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use ParamsTest\Integration\ReviewScore;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetType;
use Params\ProcessedValues;
use Params\DataLocator\DataStorage;

/**
 * @coversNothing
 * @group wip
 */
class GetTypeTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetType
     */
    public function testWorks()
    {
        $validator = new ProcessedValues();

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
}
