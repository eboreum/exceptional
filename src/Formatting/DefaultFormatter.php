<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Annotation\DebugIdentifier;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;

/**
 * {@inheritDoc}
 *
 * Multiple lines with headings and multiple levels of indented contents.
 */
class DefaultFormatter extends AbstractFormatter
{
    /**
     * The characters utilized for indenting.
     *
     * @DebugIdentifier
     */
    protected string $indentationCharacters = '    ';

    public function __construct(CasterInterface $caster)
    {
        $this->caster = $caster;
    }

    /**
     * {@inheritDoc}
     */
    public function format(\Throwable $throwable): string
    {
        $result = Caster::makeNormalizedClassName(new \ReflectionObject($throwable));

        if (
            $this->isProvidingTimestamp()
            && 0 === $this->getPreviousThrowableLevel()
        ) {
            $result .= ' (' . date('c') . ')';
        }

        $indentation = $this->makeIndentation($this->getPreviousThrowableLevel());
        $indentationNextLevel = $this->makeIndentation($this->getPreviousThrowableLevel() + 1);
        $messageLines = static::splitTextLinesToArray($throwable->getMessage());
        $stacktraceLines = static::splitTextLinesToArray($throwable->getTraceAsString());

        assert(is_array($messageLines));
        assert(is_array($stacktraceLines));

        $result .= "\n{$indentation}Message:";
        $result .= "\n" . implode("\n", array_map(
            function (string $line) use ($indentationNextLevel) {
                return $indentationNextLevel . $this->maskString($line);
            },
            $messageLines,
        ));
        $result .= "\n{$indentation}File: " . $this->normalizeFilePath($throwable->getFile());
        $result .= "\n{$indentation}Line: " . $throwable->getLine();
        $result .= "\n{$indentation}Code: " . $throwable->getCode();
        $result .= "\n{$indentation}Stacktrace:\n" . implode("\n", array_map(
            function (string $line) use ($indentationNextLevel) {
                return $indentationNextLevel . $this->maskString($line);
            },
            $stacktraceLines,
        ));

        if ($throwable->getPrevious()) {
            $maximumPreviousDepth = $this->getMaximumPreviousDepth();
            $previousCount = $this->countPreviousThrowables($throwable);

            if (is_int($maximumPreviousDepth) && $this->getPreviousThrowableLevel() >= $maximumPreviousDepth) {
                $result .= sprintf(
                    "\n%sPrevious: (%d more) (omitted)",
                    $indentation,
                    $previousCount,
                );
            } else {
                $child = $this->withPreviousThrowableLevel($this->getPreviousThrowableLevel() + 1);

                $result .= sprintf(
                    "\n%sPrevious: (%d more)",
                    $indentation,
                    $previousCount,
                );

                $result .= sprintf(
                    "\n%s%s",
                    $indentationNextLevel,
                    $child->format($throwable->getPrevious()),
                );
            }
        } else {
            $result .= "\n{$indentation}Previous: (None)";
        }

        return $result;
    }

    public function makeIndentation(int $level): string
    {
        return str_repeat(
            $this->getIndentationCharacters(),
            max(
                0,
                $level,
            ),
        );
    }

    /**
     * Returns a clone.
     */
    public function withIndentationCharacters(string $indentationCharacters): DefaultFormatter
    {
        $clone = clone $this;
        $clone->indentationCharacters = $indentationCharacters;

        return $clone;
    }

    public function getIndentationCharacters(): string
    {
        return $this->indentationCharacters;
    }
}
