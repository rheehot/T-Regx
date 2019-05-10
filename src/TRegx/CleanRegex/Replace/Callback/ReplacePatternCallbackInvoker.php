<?php
namespace TRegx\CleanRegex\Replace\Callback;

use TRegx\CleanRegex\Internal\InternalPattern as Pattern;
use TRegx\CleanRegex\Internal\Model\Matches\IRawMatchesOffset;
use TRegx\CleanRegex\Internal\Model\Matches\RawMatchesOffset;
use TRegx\CleanRegex\Internal\Subjectable;
use TRegx\CleanRegex\Replace\NonReplaced\NonReplacedStrategy;
use TRegx\SafeRegex\preg;

class ReplacePatternCallbackInvoker
{
    /** @var Pattern */
    private $pattern;
    /** @var Subjectable */
    private $subject;
    /** @var int */
    private $limit;
    /** @var NonReplacedStrategy */
    private $strategy;

    public function __construct(Pattern $pattern, Subjectable $subject, int $limit, NonReplacedStrategy $strategy)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->limit = $limit;
        $this->strategy = $strategy;
    }

    public function invoke(callable $callback): string
    {
        $result = $this->pregReplaceCallback($callback, $replaced);
        if ($replaced === 0) {
            return $this->strategy->replacementResult($this->subject->getSubject()) ?? $result;
        }
        return $result;
    }

    public function pregReplaceCallback(callable $callback, ?int &$replaced): string
    {
        return preg::replace_callback($this->pattern->pattern,
            $this->getObjectCallback($callback),
            $this->subject->getSubject(),
            $this->limit,
            $replaced);
    }

    private function getObjectCallback(callable $callback): callable
    {
        if ($this->limit === 0) {
            return function () {
            };
        }
        return $this->createObjectCallback($callback);
    }

    private function createObjectCallback(callable $callback): callable
    {
        $object = new ReplaceCallbackObject($callback, $this->subject, $this->analyzePattern(), $this->limit);
        return $object->getCallback();
    }

    private function analyzePattern(): IRawMatchesOffset
    {
        preg::match_all($this->pattern->pattern, $this->subject->getSubject(), $matches, PREG_OFFSET_CAPTURE);
        return new RawMatchesOffset($matches);
    }
}
