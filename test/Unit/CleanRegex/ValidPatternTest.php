<?php
namespace Test\Unit\CleanRegex;

use CleanRegex\Internal\Pattern;
use CleanRegex\ValidPattern;
use PHPUnit\Framework\TestCase;
use SafeRegex\Errors\Errors\EmptyHostError;
use SafeRegex\Errors\ErrorsCleaner;

class ValidPatternTest extends TestCase
{
    /**
     * @test
     * @dataProvider validPatterns
     * @param string $string
     */
    public function shouldValidatePattern(string $string)
    {
        // given
        $pattern = new ValidPattern(new Pattern($string));

        // when
        $isValid = $pattern->isValid();

        // then
        $this->assertTrue($isValid, "Failed asserting that pattern is valid");
    }

    public function validPatterns()
    {
        return [
            ['~((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s | $)~'],
            ['!exclamation marks!'],
        ];
    }

    /**
     * @test
     * @dataProvider \Test\DataProviders::invalidPregPatterns()
     * @param string $string
     */
    public function shouldNotValidatePattern(string $string)
    {
        // given
        $pattern = new ValidPattern(new Pattern($string));

        // when
        $isValid = $pattern->isValid();

        // then
        $this->assertFalse($isValid, "Failed asserting that pattern is invalid");
    }

    /**
     * @test
     * @dataProvider \Test\DataProviders::invalidPregPatterns()
     * @param string $string
     */
    public function shouldNotLeaveErrors(string $string)
    {
        // given
        $pattern = new ValidPattern(new Pattern($string));
        $errorsCleaner = new ErrorsCleaner();

        // when
        $pattern->isValid();
        $error = $errorsCleaner->getError();

        // then
        $this->assertInstanceOf(EmptyHostError::class, $error);
        $this->assertFalse($error->occurred());
    }
}