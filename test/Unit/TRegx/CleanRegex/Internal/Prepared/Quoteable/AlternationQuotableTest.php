<?php
namespace Test\Unit\TRegx\CleanRegex\Internal\Prepared\Quoteable;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Internal\Prepared\Quoteable\AlternationQuotable;

class AlternationQuotableTest extends TestCase
{
    /**
     * @test
     */
    public function shouldQuote()
    {
        // given
        $quotable = new AlternationQuotable(['/()', '^#$'], null);

        // when
        $result = $quotable->quote('');

        // then
        $this->assertEquals('(?:/\(\)|\^\#\$)', $result);
    }

    /**
     * @test
     */
    public function shouldQuoteDelimiter()
    {
        // given
        $quotable = new AlternationQuotable(['a', '%b'], null);

        // when
        $result = $quotable->quote('%');

        // then
        $this->assertEquals('(?:a|\%b)', $result);
    }

    /**
     * @test
     */
    public function shouldRemoveDuplicates_caseSensitive()
    {
        // given
        $quotable = new AlternationQuotable(['a', 'FOO', 'a', 'c', 'foo'], $this->identity());

        // when
        $result = $quotable->quote(''); // or should it throw maybe?

        // then
        $this->assertEquals('(?:a|FOO|c|foo)', $result);
    }

    /**
     * @test
     */
    public function shouldRemoveDuplicates_caseInsensitive()
    {
        // given
        $quotable = new AlternationQuotable(['a', 'FOO', 'a', 'a', 'c', 'foo'], 'strtolower');

        // when
        $result = $quotable->quote('');

        // then
        $this->assertEquals('(?:a|FOO|c)', $result);
    }

    /**
     * @test
     */
    public function shouldAddAnEmptyProduct_toIndicateAnEmptyString()
    {
        // given
        $quotable = new AlternationQuotable(['a', '', '', 'b'], null);

        // when
        $result = $quotable->quote('');

        // then
        $this->assertEquals('(?:a|b|)', $result);
    }

    /**
     * @test
     */
    public function shouldIgnoreOtherCharacters()
    {
        // given
        $quotable = new AlternationQuotable(['|', ' ', '0'], null);

        // when
        $result = $quotable->quote('');

        // then
        $this->assertEquals('(?:\|| |0)', $result);
    }

    private function identity(): callable
    {
        return function ($a) {
            return $a;
        };
    }
}
