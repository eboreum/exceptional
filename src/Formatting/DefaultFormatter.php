<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use ReflectionObject;
use Throwable;

use function array_map;
use function assert;
use function date;
use function implode;
use function is_array;
use function is_int;
use function max;
use function sprintf;
use function str_repeat;

/**
 * {@inheritDoc}
 *
 * Multiple lines with headings and multiple levels of indented contents.
 */
class DefaultFormatter extends AbstractFormatter
{
    /**
     * The characters utilized for indenting.
     */
    #[DebugIdentifier]
    protected string $indentationCharacters = '    ';

    public function __construct(CasterInterface $caster)
    {
        $this->caster = $caster;
    }

    public function format(Throwable $throwable): string
    {
        $result = Caster::makeNormalizedClassName(new ReflectionObject($throwable));

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

        $result .= sprintf(
            "\n%sMessage:",
            $indentation,
        );
        $result .= "\n" . implode("\n", array_map(
            function (string $line) use ($indentationNextLevel) {
                return $indentationNextLevel . $this->maskString($line);
            },
            $messageLines,
        ));
        $result .= sprintf(
            "\n%sFile: %s",
            $indentation,
            $this->normalizeFilePath($throwable->getFile()),
        );
        $result .= sprintf(
            "\n%sLine: %s",
            $indentation,
            $throwable->getLine(),
        );
        $result .= sprintf(
            "\n%sCode: %s",
            $indentation,
            $throwable->getCode(),
        );
        $result .= sprintf(
            "\n%sStacktrace:\n%s",
            $indentation,
            implode("\n", array_map(
                function (string $line) use ($indentationNextLevel) {
                    return $indentationNextLevel . $this->maskString($line);
                },
                $stacktraceLines,
            )),
        );

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
            $result .= sprintf(
                "\n%sPrevious: (None)",
                $indentation,
            );
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
