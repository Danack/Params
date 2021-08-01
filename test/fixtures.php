<?php /** @noinspection ALL */

declare(strict_types=1);


use Params\ExtractRule\GetString;
use Params\ExtractRule\GetStringOrDefault;
use Params\InputParameterList;
use Params\InputParameter;
use Params\Param;
use Params\ProcessRule\AlwaysErrorsRule;
use Params\ProcessRule\ImagickIsRgbColor;
use Params\SafeAccess;
use ParamsTest\ImagickColorParam;
use Params\Create\CreateFromArray;
use Params\Create\CreateOrErrorFromArray;

class TestObject
{
    private string $name;
    private int $age;

    public function __construct(
        string $name,
        int $age
    ) {
        $this->name = $name;
        $this->age = $age;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }
}

class DoesNotImplementInputParameterList
{
}


class ReturnsBadInputParameterList implements InputParameterList
{
    public static function getInputParameterList(): array
    {
        return [
            // Wrong type
            new StdClass()
        ];
    }
}

class TestParams implements InputParameterList
{
    private string $name;

    /**
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
                'name',
                new GetString(),
            )
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}


class AlwaysErrorsParams implements InputParameterList
{
    public const ERROR_MESSAGE = 'Forced error';

    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
                'foo',
                new GetString(),
            ),
            new InputParameter(
                'bar',
                new GetString(),
                new AlwaysErrorsRule(self::ERROR_MESSAGE)
            )
        ];
    }
}


class ThreeColors implements InputParameterList
{
    use SafeAccess;
    use Params\Create\CreateFromVarMap;
    use Params\InputParameterListFromAttributes;

    public function __construct(
        #[ImagickColorParam('rgb(225, 225, 225)')]
        private string $background_color,
        #[ImagickColorParam('rgb(0, 0, 0)')]
        private string $stroke_color,
        #[ImagickColorParam('DodgerBlue2')]
        private string $fill_color
    ) {
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }

    public function getFillColor(): string
    {
        return $this->fill_color;
    }
}


class NotActuallyAParam
{
    public function __construct(
        private string $name,
        private string $default
    ) {
    }
}

class NotAParameter
{
}



class OneColor
{
    use SafeAccess;
    use CreateFromArray;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    public function __construct(string $stroke_color, string $background_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }
}


class TwoColors
{
    use SafeAccess;
    use CreateOrErrorFromArray;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[NotActuallyAParam('fill_color', 'rgb(0, 0, 0)')]
    private string $fill_color;

    #[ImagickColorParam('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    #[NotAParameter()]
    private $non_param_property;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $stroke_color, string $background_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }
}


class OneColorWithOtherAnnotationThatIsNotAParam
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[NotActuallyAParam('stroke_color', 'rgb(0, 0, 0)')]
    private string $stroke_color;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $background_color)
    {
        $this->background_color = $background_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }
}



class OneColorWithOtherAnnotationThatDoesNotExist
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ThisClassDoesNotExistParam('stroke_color', 'rgb(0, 0, 0)')]
    private string $stroke_color;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $background_color)
    {
        $this->background_color = $background_color;
    }
}


class ThreeColorsMissingConstructorParam
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ImagickColorParam('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    #[ImagickColorParam('DodgerBlue2', 'fill_color')]
    private string $fill_color;

    public function __construct(string $background_color, string $stroke_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }
}




class ThreeColorsMissingPropertyParam
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ImagickColorParam('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    private string $fill_color;

    public function __construct(string $background_color, string $stroke_color, string $fill_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
        $this->fill_color = $fill_color;
    }
}


class OneColorNoConstructor
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;
}

class ThreeColorsPrivateConstructor
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    /**
     * ThreeColorsPrivateConstructor constructor.
     * @param string $background_color
     */
    private function __construct(string $background_color)
    {
        $this->background_color = $background_color;
    }
}



class ThreeColorsIncorrectParamName
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    private string $background_color;

    #[ImagickColorParam('rgb(0, 0, 0)', 'stroke_color')]
    private string $stroke_color;

    #[ImagickColorParam('rgb(0, 0, 255)', 'fill_color')]
    private string $fill_color;

    public function __construct(string $background_color, string $stroke_color, string $solid_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
        $this->fill_color = $solid_color;
    }
}



class MultipleParamAnnotations
{
    use SafeAccess;

    #[ImagickColorParam('rgb(225, 225, 225)', 'background_color')]
    #[ImagickColorParam('rgb(225, 225, 225)', 'fill_color')]
    private string $background_color;

    /**
     * OneColor constructor.
     * @param string $background_color
     * @param string $stroke_color
     */
    public function __construct(string $background_color)
    {
        $this->background_color = $background_color;
        $this->stroke_color = $stroke_color;
    }

    public function getBackgroundColor(): string
    {
        return $this->background_color;
    }

    public function getStrokeColor(): string
    {
        return $this->stroke_color;
    }
}

class AttributesExistsNoConstructor
{

}

class AttributesExistsHasConstructor
{
    public function __construct(private int $foo)
    {
    }

    /**
     * @return int
     */
    public function getFoo(): int
    {
        return $this->foo;
    }
}


class AttributesExistsHasConstructorWithName
{
    public function __construct(private int $foo, private string $name)
    {
    }

    public function getFoo(): int
    {
        return $this->foo;
    }

    public function getName(): string
    {
        return $this->name;
    }
}


class ReflectionClassOfAttributeObject
{
    #[AttributeNotExist()]
    private $attribute_not_exists;

    #[AttributesExistsNoConstructor()]
    private $attribute_exists_no_constructor;

    #[AttributesExistsHasConstructor(10)]
    private $attribute_exists_has_constructor;

    #[AttributesExistsHasConstructorWithName(10)]
    private $attribute_exists_has_constructor_with_name;
}
