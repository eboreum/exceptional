<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;

/**
 * Implementing class will make a human readable version of the contents of a \Throwable instance.
 */
interface FormatterInterface
{
    /**
     * Must return the number of previous throwables (using `getPrevious`), excluding the $throwable argument.
     */
    public function countPreviousThrowables(\Throwable $throwable): int;

    /**
     * Must make a string representation of the provided \Throwable and return that as a string.
     */
    public function format(\Throwable $throwable): string;

    /**
     * Must return a clone.
     */
    public function withIsProvidingTimestamp(bool $isProvidingTimestamp): FormatterInterface;

    /**
     * Must return a clone.
     */
    public function withMaximumPreviousDepth(?int $maximumPreviousDepth): FormatterInterface;

    /**
     * Must return a clone.
     *
     * @param int $previousThrowableLevel       Must be >= 0. Otherwise, an exception must be thrown.
     * @throws RuntimeException
     */
    public function withPreviousThrowableLevel(int $previousThrowableLevel): FormatterInterface;

    /**
     * Retrieve the caster which should be used internally for rendering safe output.
     */
    public function getCaster(): CasterInterface;

    /**
     * Retrieve the current previous exception level.
     */
    public function getPreviousThrowableLevel(): int;

    /**
     * When `true`, a timestamp should be provided with the formatted throwable (commonly date('c')).
     */
    public function isProvidingTimestamp(): bool;
}
