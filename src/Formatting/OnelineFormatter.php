<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use ReflectionObject;
use Throwable;

use function assert;
use function date;
use function is_int;
use function is_string;
use function preg_replace;
use function sprintf;

/**
 * {@inheritDoc}
 *
 * Output is ensure to be on a single line. Good for error logs.
 */
class OnelineFormatter extends AbstractFormatter
{
    public function __construct(CasterInterface $caster)
    {
        $this->caster = $caster;
    }

    public function format(Throwable $throwable): string
    {
        $result = $this->normalizeString(Caster::makeNormalizedClassName(
            new ReflectionObject($throwable)
        ));

        if ($this->isProvidingTimestamp()) {
            $result .= ' (' . $this->normalizeString(date('c')) . ')';
        }

        $result .= '. Message: ' . $this->maskString($this->normalizeString($throwable->getMessage()));
        $result .= '. File: ' . $this->normalizeString($this->normalizeFilePath($throwable->getFile()));
        $result .= '. Line: ' . $throwable->getLine();
        $result .= '. Code: ' . $throwable->getCode();
        $result .= '. Stacktrace: ' . $this->normalizeString($this->maskString($throwable->getTraceAsString()));

        if ($throwable->getPrevious()) {
            $maximumPreviousDepth = $this->getMaximumPreviousDepth();
            $previousCount = $this->countPreviousThrowables($throwable);

            if (is_int($maximumPreviousDepth) && $this->getPreviousThrowableLevel() >= $maximumPreviousDepth) {
                $result .= sprintf(
                    '. Previous: (%d more) (omitted)',
                    $previousCount,
                );
            } else {
                $child = $this->withPreviousThrowableLevel($this->getPreviousThrowableLevel() + 1);

                $result .= sprintf(
                    '. Previous: (%d more) %s',
                    $previousCount,
                    $this->normalizeString($child->format($throwable->getPrevious())),
                );
            }
        } else {
            $result .= '. Previous: (None)';
        }

        return $result;
    }

    public function normalizeString(string $str): string
    {
        $replaced = preg_replace(
            '/(\r\n|\r|\n)/',
            ' ',
            $str,
        );

        assert(is_string($replaced)); // Make phpstan happy

        return $replaced;
    }
}
