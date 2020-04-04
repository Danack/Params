<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\StandardDataLocator;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\SaneCharacters;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

function getRawCharacters($string)
{
    $resultInHex = bin2hex($string);
    $resultSeparated = implode(', ', str_split($resultInHex, 2)); //byte safe

    return $resultSeparated;
}

/**
 * @coversNothing
 */
class SaneCharacterTest extends BaseTestCase
{
    public function provideSuccessCases()
    {
        return [
            ["John Smith"],
            ["Basic punctuation:'\".⁋′″‴‵‶‷"],
            ["ÁGUEDA"],
            ["ALÍCIA"],
            ["☺😎😋😂"], // emoticons \u{1F600}-\u{1F64F}
            ["✅✨❕"], // Dingbats ( 2702 - 27B0 )
            ["🚅🚲🚤"], // Transport and map symbols ( 1F680 - 1F6C0 )
            ["🆕🇯🇵🉑"],    //Enclosed characters ( 24C2 - 1F251 )
            ["⁉4⃣⌛"], // Uncategorized
            ["😀😶😕"],           // Additional emoticons ( 1F600 - 1F636 )
            ["🚍🚛🚛"],         // Additional transport and map symbols
            ["🕜🐇🕝"], // Other additional symbols
        ];
    }

    public function provideFailureCases()
    {
        return [
            ["a̧͈͖r͒͑"],
//            [" ͎a̧͈͖r̽̾̈́͒͑e"],
//            ["TO͇̹̺ͅƝ̴ȳ̳ TH̘Ë͖́̉ ͠P̯͍̭O̚​N̐Y̡ H̸̡̪̯ͨ͊̽̅̾̎Ȩ̬̩̾͛ͪ̈́̀́͘"],
//            ["C̷̙̲̝͖ͭ̏ͥͮ͟Oͮ͏̮̪̝͍M̲̖͊̒ͪͩͬ̚̚͜Ȇ̴̟̟͙̞ͩ͌͝S̨̥̫͎̭ͯ̿̔̀ͅ"],
        ];
    }

    /**
     * @dataProvider provideSuccessCases
     * @covers \Params\ProcessRule\SaneCharacters
     */
    public function testValidationSuccess($testValue)
    {
        $rule = new SaneCharacters();
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator,
            $dataLocator
        );
        $this->assertEmpty($validationResult->getValidationProblems());
    }

    /**
     * @dataProvider provideFailureCases
     * @covers \Params\ProcessRule\SaneCharacters
     */
    public function testValidationErrors($testValue)
    {
        $rule = new SaneCharacters();
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator,
            $dataLocator
        );

//        $bytesString = "Bytes were[" . getRawCharacters($testValue) . "]";

        $this->assertExpectedValidationProblems(
            $validationResult->getValidationProblems(),
            "Should have been error: " . json_encode($testValue)
        );
    }


    public function testPositionIsCorrect()
    {
        $testValue = "danack_a̧͈͖r͒͑_more_a̧͈͖r͒͑";
        $rule = new SaneCharacters();
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator,
            $dataLocator
        );
//        $messages = $validationResult->getValidationProblems();

//        $this->assertEquals(
//            "Invalid combining characters found at position 8",
//            $messages['/foo']
//        );

        $this->assertCount(1, $validationResult->getValidationProblems());

        $this->assertValidationProblem(
            '/',
            "Invalid combining characters found at position 8",
            $validationResult->getValidationProblems()
        );
    }
}
