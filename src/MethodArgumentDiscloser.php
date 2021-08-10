<?php

declare(strict_types=1);

namespace Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;

/**
 * {@inheritDoc}
 */
class MethodArgumentDiscloser extends AbstractFunctionArgumentDiscloser
{
    /**
     * @param array<int, mixed> $methodArgumentValues
     *                                          The actual values which have been passed to the function referred to in
     *                                          the $reflectionMethod argument. These values may/should be produced by
     *                                          the function `func_get_args`.
     *                                          @see https://www.php.net/manual/en/function.func-get-args.php
     */
    public function __construct(
        CasterInterface $caster,
        \ReflectionMethod $reflectionMethod,
        array $methodArgumentValues
    )
    {
        try {
            $errorMessages = [];

            if (count($methodArgumentValues) < $reflectionMethod->getNumberOfRequiredParameters()) {
                $errorMessages[] = sprintf(
                    implode("", [
                        "Argument \$methodArgumentValues = %s contains fewer elements (%d) than the required number of",
                        " parameters (%d) in argument \$reflectionMethod = (object) %s (%s%s%s), which is bogus",
                    ]),
                    Caster::getInstance()->castTyped($methodArgumentValues),
                    count($methodArgumentValues),
                    $reflectionMethod->getNumberOfRequiredParameters(),
                    Caster::makeNormalizedClassName(new \ReflectionObject($reflectionMethod)),
                    Caster::makeNormalizedClassName($reflectionMethod->getDeclaringClass()),
                    (
                        $reflectionMethod->isStatic()
                        ? "::"
                        : "->"
                    ),
                    $reflectionMethod->getName(),
                );
            }

            if ($errorMessages) {
                throw new RuntimeException(implode(". ", $errorMessages));
            }

            $this->caster = $caster;
            $this->reflectionFunction = $reflectionMethod;
            $this->functionArgumentValues = $methodArgumentValues;
        } catch (\Throwable $t) {
            /*
             * Do NOT use ExceptionMessageGenerator->makeFailureInMethodMessage here or risk endless cyclic recursion.
             */

            $argumentsAsStrings = [];
            $argumentsAsStrings[] = sprintf(
                "\$caster = %s",
                Caster::getInstance()->castTyped($caster),
            );
            $argumentsAsStrings[] = sprintf(
                "\$reflectionMethod = %s",
                Caster::getInstance()->castTyped($reflectionMethod),
            );
            $argumentsAsStrings[] = sprintf(
                "\$methodArgumentValues = %s",
                Caster::getInstance()->castTyped($methodArgumentValues),
            );

            throw new RuntimeException(sprintf(
                "Failed to construct %s with arguments {%s}",
                Caster::makeNormalizedClassName(new \ReflectionObject($this)),
                implode(", ", $argumentsAsStrings),
            ), 0, $t);
        }
    }

    public function getReflectionFunction(): \ReflectionMethod
    {
        return $this->reflectionFunction;
    }

    public static function getDefaultValueConstantRegex(): string
    {
        return implode("", [
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
                    '(',
                        '?<scope>(parent|self)',
                    ')',
                    '::',
                    '(',
                        '?<scopedName>([a-zA-Z_]\w*)',
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
