<?php
namespace Test\Feature\TRegx\CleanRegex\Match\fluent;

use PHPUnit\Framework\TestCase;
use Test\Feature\TRegx\CleanRegex\Replace\by\group\CustomException;
use TRegx\CleanRegex\Exception\NoFirstElementFluentException;
use TRegx\CleanRegex\Match\Details\Group\MatchGroup;
use TRegx\CleanRegex\Match\Details\Match;

class AbstractMatchPatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFluent()
    {
        // when
        $result = pattern("(?<capital>[A-Z])?[\w']+")
            ->match("I'm rather old, He likes Apples")
            ->fluent()
            ->filter(function (Match $match) {
                return $match->textLength() !== 3;
            })
            ->map(function (Match $match) {
                return $match->group('capital');
            })
            ->map(function (MatchGroup $matchGroup) {
                if ($matchGroup->matched()) {
                    return "yes: $matchGroup";
                }
                return "no";
            })
            ->all();

        // then
        $this->assertEquals(['no', 'yes: H', 'no', 'yes: A'], $result);
    }

    /**
     * @test
     */
    public function shouldFluent_passUserData()
    {
        // given
        pattern("\w+")
            ->match("Foo, Bar")
            ->fluent()
            ->filter(function (Match $match) {
                // when
                $match->setUserData($match === 'Foo' ? 'hey' : 'hello');

                return true;
            })
            ->forEach(function (Match $match) {
                // then
                $userData = $match->getUserData();

                $this->assertEquals($match === 'Foo' ? 'hey' : 'hello', $userData);
            });
    }

    /**
     * @test
     */
    public function shouldFluent_findFirst()
    {
        // when
        pattern("(?<capital>[A-Z])?[\w']+")
            ->match("I'm rather old, He likes Apples")
            ->fluent()
            ->filter(function (Match $match) {
                return $match->textLength() !== 3;
            })
            ->findFirst(function (Match $match) {
                $this->assertTrue(true);
            })
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldFluent_findFirst_orThrow()
    {
        // then
        $this->expectException(NoFirstElementFluentException::class);
        $this->expectExceptionMessage("Expected to get the first element from fluent pattern, but the elements feed is empty");

        // when
        pattern("Foo")
            ->match("Bar")
            ->fluent()
            ->findFirst(function (Match $match) {
                $this->assertTrue(false);
            })
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldFluent_findFirst_orThrow_custom()
    {
        // then
        $this->expectException(CustomException::class);
        $this->expectExceptionMessage("Expected to get the first element from fluent pattern, but the elements feed is empty");

        // when
        pattern("Foo")
            ->match("Bar")
            ->fluent()
            ->findFirst(function (Match $match) {
                $this->assertTrue(false);
            })
            ->orThrow(CustomException::class);
    }
}
