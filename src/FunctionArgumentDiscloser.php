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
     * @see https://www.php.net/manual/en/function.func-get-args.php
     *
     * @param array<int, mixed> $functionArgumentValues The actual values which have been passed to the function
     *                                                  referred to in the $reflectionFunction argument. These values
     *                                                  may/should be produced by the function `func_get_args`.
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
            /*
             * Do NOT use ExceptionMessageGenerator->makeFailureInMethodMessage here or risk endless cyclic recursion.
             */

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

    public static function getDefaultValueConstantRegex(): string
    {
        $phpClassNameRegexInner = static::getPHPClassNameRegexInner();

        return sprintf(
            implode('', [
                '/',
                '^',
                '(',
                    '(',
                        '?<globalName>(%s)',
                    ')',
                    '|',
                    '(',
                        '?<namespacedName>(',
                            '%s(',
                                '\\\\%s',
                            ')*',
                            '\\\\%s',
                        ')',
                    ')',
                    '|',
                    '(',
                        '\\\\?',
                        '(',
                            '?<className>(%s(\\\\%s)*)',
                        ')',
                        '::',
                        '(',
                            '?<classConstantName>(%s)',
                        ')',
                    ')',
                ')',
                '$',
                '/',
            ]),
            $phpClassNameRegexInner,
            $phpClassNameRegexInner,
            $phpClassNameRegexInner,
            $phpClassNameRegexInner,
            $phpClassNameRegexInner,
            $phpClassNameRegexInner,
            $phpClassNameRegexInner,
        );
    }

    /**
     * @see https://www.php.net/manual/en/language.variables.basics.php
     */
    public static function getPHPClassNameRegexInner(): string
    {
        return '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
    }

    public function getReflectionFunction(): \ReflectionFunction
    {
        assert($this->reflectionFunction instanceof \ReflectionFunction); // Make phpstan happy

        return $this->reflectionFunction;
    }
}
