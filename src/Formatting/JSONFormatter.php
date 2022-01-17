<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\ExceptionMessageGenerator;

/**
 * {@inheritDoc}
 *
 * Formats a \Throwable to JSON.
 */
class JSONFormatter extends AbstractFormatter
{
    #[DebugIdentifier]
    protected CharacterEncoding $characterEncoding;

    protected int $flags = 0;

    /** @var int<1, max> */
    protected int $depth = 512;

    public function __construct(CasterInterface $caster, CharacterEncoding $characterEncoding)
    {
        $this->caster = $caster;
        $this->characterEncoding = $characterEncoding;
    }

    /**
     * @param int $jsonErrorCode Corresponds to value returned by 'json_last_error()'.
     */
    public static function errorCodeToText(int $jsonErrorCode): ?string
    {
        return static::getErrorCodeToTextMap()[$jsonErrorCode] ?? null;
    }

    /**
     * @return array<int, string>
     */
    public static function getErrorCodeToTextMap(): array
    {
        return [
            JSON_ERROR_NONE => 'JSON_ERROR_NONE',
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8', // PHP 5.3.3
            JSON_ERROR_RECURSION => 'JSON_ERROR_RECURSION', // PHP 5.5.0
            JSON_ERROR_INF_OR_NAN => 'JSON_ERROR_INF_OR_NAN', // PHP 5.5.0
            JSON_ERROR_UNSUPPORTED_TYPE => 'JSON_ERROR_UNSUPPORTED_TYPE', // PHP 5.5.0
            JSON_ERROR_INVALID_PROPERTY_NAME => 'JSON_ERROR_INVALID_PROPERTY_NAME', // PHP 7.0.0
            JSON_ERROR_UTF16 => 'JSON_ERROR_UTF16', // PHP 7.0.0
        ];
    }

    /**
     * {@inheritDoc}
     *
     * Returns JSON.
     *
     * @throws RuntimeException
     */
    public function format(\Throwable $throwable): string
    {
        try {
            $stdClass = $this->formatInner($throwable, $this);

            try {
                $json = json_encode($stdClass, $this->getFlags(), $this->getDepth());
            } catch (\Throwable $t) {
                throw new RuntimeException(sprintf(
                    'Failure when calling: json_encode(%s, %s, %s)',
                    $this->getCaster()->castTyped($stdClass),
                    $this->getCaster()->castTyped($this->getFlags()),
                    $this->getCaster()->castTyped($this->getDepth()),
                ), 0, $t);
            }

            if (false === is_string($json)) {
                $jsonErrorCode = json_last_error();

                if (JSON_ERROR_NONE !== $jsonErrorCode) {
                    $errorName = static::errorCodeToText($jsonErrorCode);

                    throw new RuntimeException(sprintf(
                        'JSON encoding failed: (%s) %s',
                        ($errorName ?? ''),
                        (json_last_error_msg() ?: '(No error message available)')
                    ));
                }
            }
        } catch (\Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        assert(is_string($json)); // Make phpstan happy

        return $json;
    }

    /**
     * @param int<1, max> $depth Must be > 0. Otherwise, a RuntimeException is thrown.
     * @throws RuntimeException
     */
    public function withDepth(int $depth): self
    {
        try {
            if (false === ($depth >= 1)) { // @phpstan-ignore-line
                throw new RuntimeException(sprintf(
                    'Expects argument $depth to be >= 1, but it is not. Found: %s',
                    Caster::getInstance()->castTyped($depth),
                ));
            }

            $clone = clone $this;
            $clone->depth = $depth;
        } catch (\Throwable $t) { // @phpstan-ignore-line
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new \ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return $clone;
    }

    public function withFlags(int $flags): JSONFormatter
    {
        $clone = clone $this;
        $clone->flags = $flags;

        return $clone;
    }

    public function getCharacterEncoding(): CharacterEncoding
    {
        return $this->characterEncoding;
    }

    /**
     * @return int<1, max>
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    protected function formatInner(\Throwable $throwable, JSONFormatter $topLevelJSONFormatter): \stdClass
    {
        $array = [
            'class' => Caster::makeNormalizedClassName(new \ReflectionObject($throwable)),
        ];

        if ($this->isProvidingTimestamp()) {
            $array['time'] = date('c');
        }

        $array['file'] = $this->normalizeFilePath($throwable->getFile());
        $array['line'] = strval($throwable->getLine());
        $array['code'] = strval($throwable->getCode());
        $array['message'] = $this->maskString($throwable->getMessage());
        $array['stacktrace'] = $this->maskString($throwable->getTraceAsString());

        if ($throwable->getPrevious()) {
            $maximumPreviousDepth = $this->getMaximumPreviousDepth();
            $previousCount = $this->countPreviousThrowables($throwable);

            if (is_int($maximumPreviousDepth) && $this->getPreviousThrowableLevel() >= $maximumPreviousDepth) {
                $array['previous'] = sprintf(
                    '%d more (omitted)',
                    $previousCount,
                );
            } else {
                $childDepth = $this->getDepth() - 1;

                if (false === ($childDepth > 0)) {
                    throw new RuntimeException(sprintf(
                        'Maximum JSON depth of %d was reached; cannot produce JSON',
                        $topLevelJSONFormatter->getDepth(),
                    ));
                }

                $child = $this->withDepth($childDepth);
                $child = $child->withPreviousThrowableLevel($this->getPreviousThrowableLevel() + 1);

                assert($child instanceof JSONFormatter);

                $array['previous'] = $child->formatInner($throwable->getPrevious(), $topLevelJSONFormatter);
            }
        } else {
            $array['previous'] = null;
        }

        return (object)$array;
    }
}
