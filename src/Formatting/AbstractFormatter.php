<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Annotation\DebugIdentifier;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Contract\DebugIdentifierAnnotationInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\ExceptionMessageGenerator;

/**
 * {@inheritDoc}
 */
abstract class AbstractFormatter implements FormatterInterface, DebugIdentifierAnnotationInterface
{
    /**
     * @DebugIdentifier
     */
    protected CasterInterface $caster;

    /**
     * The previous throwable level the formatter is currently at. Must be >= 0.
     *
     * @DebugIdentifier
     */
    protected int $previousThrowableLevel = 0;

    /**
     * The number of previous exception the formatter will include. `null` means there is no limit.
     *
     * @DebugIdentifier
     */
    protected ?int $maximumPreviousDepth = null;

    /**
     * A timestamp for when the exception was formatted.
     *
     * @DebugIdentifier
     */
    protected bool $isProvidingTimestamp = false;

    /**
     * {@inheritDoc}
     */
    public function countPreviousThrowables(\Throwable $throwable): int
    {
        $count = 0;
        $currentThrowable = $throwable->getPrevious();

        while ($currentThrowable) {
            ++$count;
            $currentThrowable = $currentThrowable->getPrevious();
        }

        return $count;
    }

    /**
     * Convenience method for targeting the caster's masking functionality.
     */
    public function maskString(string $text): string
    {
        return $this->getCaster()->maskString($text);
    }

    /**
     * Converts backslashes (Windows) to forward slashes.
     */
    public function normalizeFilePath(string $filePath): string
    {
        return str_replace(
            '\\',
            '/',
            $filePath,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function withCaster(CasterInterface $caster): FormatterInterface
    {
        $clone = clone $this;
        $clone->caster = $caster;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withIsProvidingTimestamp(bool $isProvidingTimestamp): FormatterInterface
    {
        $clone = clone $this;
        $clone->isProvidingTimestamp = $isProvidingTimestamp;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function withMaximumPreviousDepth(?int $maximumPreviousDepth): FormatterInterface
    {
        $clone = clone $this;
        $clone->maximumPreviousDepth = null;

        if (is_int($maximumPreviousDepth)) {
            $clone->maximumPreviousDepth = max(0, $maximumPreviousDepth);
        }

        return $clone;
    }

    /**
     * Returns a clone.
     */
    public function withPreviousThrowableLevel(int $previousThrowableLevel): FormatterInterface
    {
        try {
            if (false === ($previousThrowableLevel >= 0)) {
                throw new RuntimeException(sprintf(
                    'Expects argument $previousThrowableLevel to be <= 0, but it is not. Found: %s',
                    Caster::getInstance()->castTyped($previousThrowableLevel),
                ));
            }

            $clone = clone $this;
            $clone->previousThrowableLevel = $previousThrowableLevel;
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod(static::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function getCaster(): CasterInterface
    {
        return $this->caster;
    }

    /**
     * {@inheritDoc}
     */
    public function getMaximumPreviousDepth(): ?int
    {
        return $this->maximumPreviousDepth;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreviousThrowableLevel(): int
    {
        return $this->previousThrowableLevel;
    }

    /**
     * {@inheritDoc}
     */
    public function isProvidingTimestamp(): bool
    {
        return $this->isProvidingTimestamp;
    }

    /**
     * @return array<int, string>
     */
    public static function splitTextLinesToArray(string $text): array
    {
        $split = preg_split('/(\r\n|\r|\n)/', $text);

        assert(is_array($split));

        return $split;
    }
}
