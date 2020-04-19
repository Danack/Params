<?php

namespace ParamsTest;

use Params\Messages;
use Params\ValidationResult;
use PHPUnit\Framework\TestCase;
use Params\ProcessedValues;

/**
 * @coversNothing
 *
 * Allows checking that no code has output characters, or left the output buffer in a bad state.
 *
 */
class BaseTestCase extends TestCase
{
    private $startLevel = null;

    public function setup(): void
    {
        $this->startLevel = ob_get_level();
        ob_start();
    }

    public function teardown(): void
    {
        if ($this->startLevel === null) {
            $this->assertEquals(0, 1, "startLevel was not set, cannot complete teardown");
        }
        $contents = ob_get_contents();
        ob_end_clean();

        $endLevel = ob_get_level();
        $this->assertEquals($endLevel, $this->startLevel, "Mismatched ob_start/ob_end calls....somewhere");
        $this->assertEquals(
            0,
            strlen($contents),
            "Something has directly output to the screen: [".substr($contents, 0, 500)."]"
        );
    }

    public function testPHPUnitApparentlyGetsConfused()
    {
        //Basically despite having:
        //<exclude>*/BaseTestCase.php</exclude>
        //in the phpunit.xml file it still thinks this file is a test class.
        //and then complains about it not having any tests.
        $this->assertTrue(true);
    }


    /**
     * @param string $identifier
     * @param string $problem
     * @param \Params\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblem(string $identifier, string $expectedProblem, $validationProblems)
    {
        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getDataLocator()->toString() !== $identifier) {
                continue;
            }

            if ($validationProblem->getProblemMessage() === $expectedProblem) {
                // correct problem message found
                return;
            }

            $incorrectMessageText = sprintf(
                "Validation problem for identifier '%s' found, but wrong message.\nExpected: '%s'\nActual '%s'\n",
                $identifier,
                $expectedProblem,
                $validationProblem->getProblemMessage()
            );

            $this->fail($incorrectMessageText);
        }

        // TODO - should be path not identifier.
        // Identifier not found
        $identifiers = [];
        foreach ($validationProblems as $validationProblem) {
            $identifiers[] = $validationProblem->getDataLocator()->toString();
        }

        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $identifiers)
        );

        $this->fail($missingIndentifierText);
    }

    /**
     * @param string $identifier
     * @param string $problem
     * @param \Params\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblemRegexp(
        string $identifier,
        string $expectedProblem,
        $validationProblems
    ) {
        $expectedProblemRegexp = stringToRegexp($expectedProblem);

        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getDataLocator()->toString() !== $identifier) {
                continue;
            }

            if (preg_match($expectedProblemRegexp, $validationProblem->getProblemMessage())) {
                // correct problem message found
                return;
            }

            $incorrectMessageText = sprintf(
                "Validation problem for identifier '%s' found, but wrong message.\nExpected: '%s'\nActual '%s'\n",
                $identifier,
                $expectedProblem,
                $validationProblem->getProblemMessage()
            );

            $this->fail($incorrectMessageText);
        }

        // Identifier not found
        $pathsAsStrings = [];
        foreach ($validationProblems as $validationProblem) {
            $pathsAsStrings[] = $validationProblem->getDataLocator()->toString();
        }

        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $pathsAsStrings)
        );

        $this->fail($missingIndentifierText);
    }


    /**
     * @param array<array{string, string}> $identifiersAndProblems
     */
    public function assertValidationProblems($identifiersAndProblems, $validationProblems)
    {
        foreach ($identifiersAndProblems as $identifierAndProblem) {
            $identifier = $identifierAndProblem[0];
            $problem = $identifierAndProblem[1];
            $this->assertValidationProblem($identifier, $problem, $validationProblems);
        }
    }


    /**
     * @param \Params\ValidationProblem[] $validationProblems
     */
    public function assertNoValidationProblems(array $validationProblems)
    {
        if (count($validationProblems) === 0) {
            return;
        }

        $message = "Failed asserting no validation problems. Actually found:";
        foreach ($validationProblems as $validationProblem) {
            $message .= "\n  " . $validationProblem->toString();
        }

        $this->fail($message);
    }

    /**
     * Test that the expected validation problems are present
     * TODO - pass the expected problems in...
     * @param \Params\ValidationProblem[] $validationProblems
     * @param string $detailMessage
     */
    public function assertExpectedValidationProblems(
        array $validationProblems,
        string $detailMessage = null
    ) {
        $failureMessage = "Validation problems weren't expected to be empty.";
        if ($detailMessage !== null) {
            $failureMessage .= $detailMessage;
        }

        if (count($validationProblems) === 0) {
            $this->fail($failureMessage);
        }
    }

    public function assertNoErrors(ValidationResult $validationResult)
    {
        $validationProblems = $validationResult->getValidationProblems();

        $message = '';

        if (count($validationProblems) !== 0) {
            foreach ($validationProblems as $validationProblem) {
                $message .= $validationProblem->toString();
            }

            $this->fail("Unexpected problems: " . $message);
        }

//        if ($validationResult->isFinalResult() !== true) {
//            $this->fail("Validation Result should be final, but isn't");
//        }
    }

    public function assertHasValue($expectedValue, $key, ProcessedValues $processedValues)
    {
        if ($processedValues->hasValue($key) !== true) {
            $this->fail("ProcessedValues does not contain a value for [$key]");
        }

        $actualValue = $processedValues->getValue($key);

        $this->assertSame($expectedValue, $actualValue, "ProcessedValues contained wrong value.");
    }

    public function assertStringRegExp($string, $message)
    {
        $regExp = stringToRegexp($string);
        $this->assertRegExp($regExp, $message);
    }
}
