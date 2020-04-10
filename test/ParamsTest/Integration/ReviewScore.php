<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\Create\CreateFromVarMap;
use Params\Create\CreateArrayOfTypeFromArray;
use Params\ExtractRule\GetInt;
use Params\ExtractRule\GetString;
use Params\InputParameter;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinLength;
use Params\SafeAccess;
use Params\InputParameterList;

class ReviewScore implements InputParameterList
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateArrayOfTypeFromArray;

    /** @var int  */
    private $score;

    /** @var string */
    private $comment;

    /**
     *
     * @param int $foo
     * @param string $bar
     */
    public function __construct(int $score, string $comment)
    {
        $this->score = $score;
        $this->comment = $comment;
    }

    /**
     * @return \Params\InputParameter[]
     */
    public static function getInputParameterList()
    {
        return [
            new InputParameter(
                'score',
                new GetInt(),
                new MaxIntValue(100)
            ),
            new InputParameter(
                'comment',
                new GetString(),
                new MinLength(4)
            ),
        ];
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
