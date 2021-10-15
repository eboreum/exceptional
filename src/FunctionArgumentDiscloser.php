<?php

declare(strict_types=1);

namespace Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Exception\RuntimeException;

/**
 * {@inheritDoc}
 */
class FunctionArgumentDiscloser extends AbstractFunctionArgumentDiscloser
{
    /**
     * @param array<int, mixed> $functionArgumentValues
     *                                                  The actual values which have been passed to the function referred to in
     *                                                  the $reflectionFunction argument. These values may/should be produced by
     *                                                  the function `func_get_args`.
     *
     *                                          @see https://www.php.net/manual/en/function.func-get-args.php
     */
    public function __construct(
        CasterInterface $caster,
        \ReflectionFunction $reflectionFunction,
        array $functionArgumentValues
    ) {
        try {
            $errorMessages = [];

            if (count($functionArgumentValues) < $reflectionFunction->getNumberOfRequiredParameters()) {
                $errorMessages[] = sprintf(
                    implode('', [
                        'Argument $functionArgumentValues = %s contains fewer elements (%d) than the required number',
                        ' of parameters (%d) in argument $reflectionFunction = (object) %s, which is bogus',
                    ]),
                    Caster::getInstance()->castTyped($functionArgumentValues),
                    count($functionArgumentValues),
                    $reflectionFunction->getNumberOfRequiredParameters(),
                    Caster::makeNormalizedClassName(new \ReflectionObject($reflectionFunction)),
                );
            }

            if ($errorMessages) {
                throw new RuntimeException(implode('. ', $errorMessages));
            }

            $this->caster = $caster;
            $this->reflectionFunction = $reflectionFunction;
            $this->functionArgumentValues = $functionArgumentValues;
        } catch (\Throwable $t) {
            // Do NOT use ExceptionMessageGenerator->makeFailureInMethodMessage here or risk endless cyclic recursion.

            $argumentsAsStrings = [];
            $argumentsAsStrings[] = sprintf(
                '$caster = %s',
                Caster::getInstance()->castTyped($caster),
            );
            $argumentsAsStrings[] = sprintf(
                '$reflectionFunction = %s',
                Caster::getInstance()->castTyped($reflectionFunction),
            );
            $argumentsAsStrings[] = sprintf(
                '$functionArgumentValues = %s',
                Caster::getInstance()->castTyped($functionArgumentValues),
            );

            throw new RuntimeException(sprintf(
                'Failed to construct %s with arguments {%s}',
                Caster::makeNormalizedClassName(new \ReflectionObject($this)),
                implode(', ', $argumentsAsStrings),
            ), 0, $t);
        }
    }

    public function getReflectionFunction(): \ReflectionFunction
    {
        assert($this->reflectionFunction instanceof \ReflectionFunction); // Make phpstan happy

        return $this->reflectionFunction;
    }

    public static function getDefaultValueConstantRegex(): string
    {
        return implode('', [
            '/',
            '^',
            '(',
            '(',
            '?<globalName>([a-zA-Z_]\w*)',
            ')',
            '|',
            '(',
            '?<namespacedName>(',
            '[a-zA-Z_]\w*(',
            '\\\\[a-zA-Z_]\w*',
            ')*',
            '\\\\[a-zA-Z_]\w*',
            ')',
            ')',
            '|',
            '(',
            '\\\\?',
            '(',
            '?<className>([a-zA-Z_]\w*(\\\\[a-zA-Z_]\w*)*)',
            ')',
            '::',
            '(',
            '?<classConstantName>([a-zA-Z_]\w*)',
            ')',
            ')',
            ')',
            '$',
            '/',
        ]);
    }
}
