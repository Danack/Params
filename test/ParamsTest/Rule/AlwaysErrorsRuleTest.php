<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\AlwaysErrorsRule;

/**
 * @coversNothing
 */
class AlwaysErrorsRuleTest extends BaseTestCase
{
    /**
     * @covers \Params\Rule\AlwaysErrorsRule
     */
    public function testUnknownFilterErrors()
    {
        $message = 'test message';
        $rule = new AlwaysErrorsRule($message);

        $result = $rule('foo', 5);

        $this->assertEquals($message, $result->getProblemMessage());
        $this->assertTrue($result->isFinalResult());
        $this->assertNull($result->getValue());
    }
}
