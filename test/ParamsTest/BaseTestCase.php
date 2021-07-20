<?php

namespace ParamsTest;

use Params\Exception\LogicException;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\Rule;
use Params\ValidationResult;
use PHPUnit\Framework\TestCase;
use Params\ProcessedValues;
use Params\OpenApi\ParamDescription;
use Danack\PHPUnitHelper\StringTemplateMatching;
use function \Danack\PHPUnitHelper\templateStringToRegExp;

/**
 * @coversNothing
 *
 * Allows checking that no code has output characters, or left the output buffer in a bad state.
 *
 */
class BaseTestCase extends TestCase
{
    use StringTemplateMatching;

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


    protected function assertProblems(
        ValidationResult $validationResult,
        array $messagesByIdentifier
    ) {
        $validationProblems = $validationResult->getValidationProblems();

        foreach ($messagesByIdentifier as $identifier => $message) {
            $this->assertValidationProblemRegexp($identifier, $message, $validationProblems);
        }
    }

    /**
     * @param string $identifier
     * @param string $problem
     * @param \Params\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblem(
        string $identifier,
        string $expectedProblem,
        $validationProblems
    ) {
        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getInputStorage()->getPath() !== $identifier) {
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

        // Identifier not found
        $identifiers = [];
        foreach ($validationProblems as $validationProblem) {
            $identifiers[] = $validationProblem->getInputStorage()->getPath();
        }

        $missingIndentifierText = sprintf(
            "Identifier '%s' not found in validation problems. Identifiers found are '%s'",
            $identifier,
            implode(", ", $identifiers)
        );

        $this->fail($missingIndentifierText);
    }

    public function assertOneErrorAndContainsString(
        ValidationResult $validationResult,
        string $needle
    ) {
        $validationProblems = $validationResult->getValidationProblems();
        $this->assertCount(1, $validationProblems);

        $onlyProblem = $validationProblems[0];
        $this->assertStringContainsString($needle, $onlyProblem->getProblemMessage());
    }

    /**
     * @param string $identifier
     * @param string $expectedProblem
     * @param \Params\ValidationProblem[] $validationProblems
     */
    protected function assertValidationProblemRegexp(
        string $identifier,
        string $expectedProblem,
        $validationProblems
    ) {
        $expectedProblemRegexp = templateStringToRegExp($expectedProblem);

        foreach ($validationProblems as $validationProblem) {
            if ($validationProblem->getInputStorage()->getPath() !== $identifier) {
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
            $pathsAsStrings[] = $validationProblem->getInputStorage()->getPath();
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

    public function assertNoProblems(ValidationResult $validationResult)
    {
        $validationProblems = $validationResult->getValidationProblems();
        $this->assertNoValidationProblems($validationProblems);
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
    }

    public function assertHasValue($expectedValue, $key, ProcessedValues $processedValues)
    {
        if ($processedValues->hasValue($key) !== true) {
            $this->fail("ProcessedValues does not contain a value for [$key]");
        }

        $actualValue = $processedValues->getValue($key);

        $this->assertSame(
            $expectedValue,
            $actualValue,
            "ProcessedValues contained wrong value."
        );
    }


    public function applyRuleToDescription(Rule $rule): ParamDescription
    {
        $description = new OpenApiV300ParamDescription('John');
        $rule->updateParamDescription($description);

        return $description;
    }
}
