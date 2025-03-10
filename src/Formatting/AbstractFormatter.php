<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\ExceptionMessageGenerator;
use ReflectionMethod;
use Throwable;

use function assert;
use function func_get_args;
use function is_array;
use function is_int;
use function max;
use function preg_split;
use function sprintf;
use function str_replace;

abstract class AbstractFormatter implements FormatterInterface, DebugIdentifierAttributeInterface
{
    /**
     * @return array<int, string>
     */
    public static function splitTextLinesToArray(string $text): array
    {
        $split = preg_split('/(\r\n|\r|\n)/', $text);

        assert(is_array($split));

        return $split;
    }

    #[DebugIdentifier]
    protected CasterInterface $caster;

    /**
     * The previous throwable level the formatter is currently at. Must be >= 0.
     */
    #[DebugIdentifier]
    protected int $previousThrowableLevel = 0;

    /**
     * The number of previous exception the formatter will include. `null` means there is no limit.
     */
    #[DebugIdentifier]
    protected ?int $maximumPreviousDepth = null;

    /**
     * A timestamp for when the exception was formatted.
     */
    #[DebugIdentifier]
    protected bool $isProvidingTimestamp = false;

    public function countPreviousThrowables(Throwable $throwable): int
    {
        $count = 0;
        $currentThrowable = $throwable->getPrevious();

        while ($currentThrowable) {
            $count++;
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

    public function withCaster(CasterInterface $caster): static
    {
        $clone = clone $this;
        $clone->caster = $caster;

        return $clone;
    }

    public function withIsProvidingTimestamp(bool $isProvidingTimestamp): static
    {
        $clone = clone $this;
        $clone->isProvidingTimestamp = $isProvidingTimestamp;

        return $clone;
    }

    public function withMaximumPreviousDepth(?int $maximumPreviousDepth): static
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
    public function withPreviousThrowableLevel(int $previousThrowableLevel): static
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
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod(self::class, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function getCaster(): CasterInterface
    {
        return $this->caster;
    }

    public function getMaximumPreviousDepth(): ?int
    {
        return $this->maximumPreviousDepth;
    }

    public function getPreviousThrowableLevel(): int
    {
        return $this->previousThrowableLevel;
    }

    public function isProvidingTimestamp(): bool
    {
        return $this->isProvidingTimestamp;
    }
}
