<?php

declare(strict_types=1);

namespace Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Contract\ImmutableObjectInterface;
use Eboreum\Exceptional\Exception\RuntimeException;

/**
 * Provides an API for extracting information about and mapping them to values for method arguments/parameters.
 *
 * You must NOT implement the interface TextuallyIdentifiableInterface on this class, because if done, things will get
 * cyclic and explode spectacularly.
 */
abstract class AbstractFunctionArgumentDiscloser implements ImmutableObjectInterface
{
    protected CasterInterface $caster;

    /** @var \ReflectionFunction|\ReflectionMethod */
    protected $reflectionFunction;

    /** @var array<int, mixed> */
    protected array $functionArgumentValues;

    /** @var array<int, mixed>|null */
    protected ?array $normalizedFunctionArgumentValues = null;

    protected ?int $requiredParameterCount = null;

    abstract public static function getDefaultValueConstantRegex(): string;

    public function getCaster(): CasterInterface
    {
        return $this->caster;
    }

    /**
     * @param \ReflectionParameter $reflectionParameter
     *                                          Must have a default value. Otherwise, a RuntimeException is thrown.
     * @throws RuntimeException
     * @return mixed
     */
    public function getDefaultValueForReflectionParameter(\ReflectionParameter $reflectionParameter)
    {
        if (false === $reflectionParameter->isDefaultValueAvailable()) {
            throw new RuntimeException(sprintf(
                'Expects argument $reflectionParameter (name: %s) to have a default value available, but it does not',
                $this->getCaster()->cast($reflectionParameter->getName()),
            ));
        }

        if ($reflectionParameter->isDefaultValueConstant()) {
            try {
                assert(is_string($reflectionParameter->getDefaultValueConstantName()));

                preg_match(
                    static::getDefaultValueConstantRegex(),
                    $reflectionParameter->getDefaultValueConstantName(),
                    $match,
                );

                if (!$match) {
                    throw new RuntimeException(sprintf(
                        implode('', [
                            'Expects default value of parameter $%s - a constant - to match',
                            ' regular expression %s, but it does not. Found: %s',
                        ]),
                        $reflectionParameter->getName(),
                        escapeshellarg(static::getDefaultValueConstantRegex()),
                        $this->getCaster()->castTyped($reflectionParameter->getDefaultValueConstantName()),
                    ));
                }

                foreach (['globalName', 'namespacedName'] as $key) {
                    if ($match[$key] ?? false) {
                        if (false === defined($match[$key])) {
                            throw new RuntimeException(sprintf(
                                'The %s constant %s is not defined',
                                (
                                    'namespacedName' === $key
                                    ? 'namespaced'
                                    : 'global'
                                ),
                                $this->getCaster()->cast($match[$key]),
                            ));
                        }

                        return constant($match[$key]);
                    }
                }

                if ($match['scopedName'] ?? false) {
                    switch ($match['scope'] ?? '') {
                        case 'parent':
                        case 'self':
                            $isParentTraversalAllowed = ($match['scope'] === ('parent'));

                            $reflectionClassCurrent = $reflectionParameter->getDeclaringClass();
                            $currentClassLevelIndex = -1;

                            while ($reflectionClassCurrent) {
                                $currentClassLevelIndex++;

                                if ($reflectionClassCurrent->hasConstant($match['scopedName'])) {
                                    $reflectionClassConstant = $reflectionClassCurrent->getReflectionConstant(
                                        $match['scopedName']
                                    );

                                    assert(is_object($reflectionClassConstant));
                                    assert($reflectionClassConstant instanceof \ReflectionClassConstant);

                                    if ($reflectionClassConstant->isPrivate()) {
                                        if (0 === $currentClassLevelIndex) {
                                            return $reflectionClassConstant->getValue();
                                        }
                                    } else {
                                        return $reflectionClassConstant->getValue();
                                    }
                                }

                                if (false === $isParentTraversalAllowed) {
                                    break;
                                }

                                $reflectionClassCurrent = $reflectionClassCurrent->getParentClass();
                            }

                            $exceptionMessage = sprintf(
                                'Unable to locate the constant %s::%s',
                                $match['scope'],
                                $match['scopedName'],
                            );

                            if (
                                $isParentTraversalAllowed
                                && $reflectionParameter->getDeclaringClass()
                                && $reflectionParameter->getDeclaringClass()->getParentClass()
                            ) {
                                $exceptionMessage .= ' or at any parent class';
                            }

                            throw new RuntimeException($exceptionMessage);
                    }

                    /*
                    * Notice: The binding "static" is not allowed in compile-time constants.
                    */

                    throw new RuntimeException(sprintf(
                        'Uncovered case for $match[\'scope\'] = %s',
                        $this->getCaster()->castTyped($match['scope']),
                    ));
                }

                if ($match['classConstantName'] ?? false) {
                    $classConstantNameFullyQuantified = sprintf(
                        '%s::%s',
                        $match['className'],
                        $match['classConstantName'],
                    );

                    /**
                     * We do NOT need to verify the visibility of the constant here, because if visibility fails, a
                     * syntax error is thrown by the PHP process itself.
                     */

                    if (false === defined($classConstantNameFullyQuantified)) {
                        throw new RuntimeException(sprintf(
                            'Class constant %s is not defined',
                            $this->getCaster()->cast($classConstantNameFullyQuantified),
                        ));
                    }

                    return constant($classConstantNameFullyQuantified);
                }

                throw new RuntimeException(sprintf(
                    'Uncovered case for constant name %s and $match = %s',
                    $this->getCaster()->cast($reflectionParameter->getDefaultValueConstantName()),
                    $this->getCaster()->castTyped($match),
                ));
            } catch (\Throwable $t) {
                $functionText = '';

                if ($reflectionParameter->getDeclaringClass()) {
                    $isStatic = false;

                    if ($reflectionParameter->getDeclaringFunction() instanceof \ReflectionMethod) {
                        $isStatic = $reflectionParameter->getDeclaringFunction()->isStatic();
                    }

                    $functionText = sprintf(
                        'method %s%s%s',
                        Caster::makeNormalizedClassName($reflectionParameter->getDeclaringClass()),
                        (
                            $isStatic
                            ? '::'
                            : '->'
                        ),
                        $reflectionParameter->getDeclaringFunction()->getName(),
                    );
                } else {
                    $functionText = sprintf(
                        'function \\%s',
                        $reflectionParameter->getDeclaringFunction()->getName(),
                    );
                }

                throw new RuntimeException(sprintf(
                    implode('', [
                        'Parameter $%s in %s has a default value, which is a constant, but',
                        ' a problem with this constant was encountered',
                    ]),
                    $reflectionParameter->getName(),
                    $functionText,
                    $reflectionParameter->getDeclaringFunction()->getName(),
                ), 0, $t);
            }
        }

        return $reflectionParameter->getDefaultValue();
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getFunctionArgumentValues(): array
    {
        return $this->functionArgumentValues;
    }

    public function getFunctionArgumentValuesCount(): int
    {
        return count($this->functionArgumentValues);
    }

    public function getNamedParameterCount(): int
    {
        return $this->getReflectionFunction()->getNumberOfParameters();
    }

    /**
     * Function `func_get_args` will not include parameters with default values when those parameters utilize their
     * default values, i.e. no value has been passed on the respective parameter(s) with default values. We need to
     * account for this. Therefore, we do two things:
     *   1. We append default values for named parameters, which have default values, but haven't been called.
     *   2. We convert a potential variadic named parameter from all variadic array elements being at the same level as
     *      all other arguments, to being an array at the index position of the variadic named parameter.
     *
     * @throws RuntimeException
     * @return array<int, mixed>
     */
    public function getNormalizedFunctionArgumentValues(): array
    {
        if (null === $this->normalizedFunctionArgumentValues) {
            $this->normalizedFunctionArgumentValues = [];
            $functionArgumentValues = array_values($this->getFunctionArgumentValues());
            $indexMax = (
                max(
                    count($functionArgumentValues),
                    $this->getNamedParameterCount(),
                )
                - 1
            );
            $indexLastNamedParameter = $this->getNamedParameterCount() - 1;

            for ($index = 0; $index <= $indexMax; $index++) {
                if ($index <= $indexLastNamedParameter) {
                    $reflectionParameter = $this->getReflectionParameterByIndex($index);

                    assert(is_object($reflectionParameter));

                    if ($index === $indexLastNamedParameter) {
                        if ($reflectionParameter->isVariadic()) {
                            $this->normalizedFunctionArgumentValues[$index] = array_slice(
                                $functionArgumentValues,
                                $index,
                            );

                            break;
                        }
                    }

                    if (array_key_exists($index, $functionArgumentValues)) {
                        $this->normalizedFunctionArgumentValues[$index] = $functionArgumentValues[$index];
                    } elseif ($reflectionParameter->isDefaultValueAvailable()) {
                        $this->normalizedFunctionArgumentValues[$index] = $this->getDefaultValueForReflectionParameter(
                            $reflectionParameter
                        );
                    } else {
                        $this->normalizedFunctionArgumentValues[$index] = null;
                    }
                } else {
                    $this->normalizedFunctionArgumentValues[$index] = $functionArgumentValues[$index];
                }
            }
        }

        return $this->normalizedFunctionArgumentValues;
    }

    public function getNormalizedFunctionArgumentValuesCount(): int
    {
        return count($this->getNormalizedFunctionArgumentValues());
    }

    public function getOptionalParameterCount(): int
    {
        return $this->getNamedParameterCount() - $this->getReflectionFunction()->getNumberOfRequiredParameters();
    }

    public function getLastNamedParameterIndex(): int
    {
        return $this->getNamedParameterCount() - 1;
    }

    /**
     * @return \ReflectionFunction|\ReflectionMethod
     */
    public function getReflectionFunction()
    {
        return $this->reflectionFunction;
    }

    public function getReflectionParameterByIndex(int $index): ?\ReflectionParameter
    {
        return ($this->getReflectionFunction()->getParameters()[$index] ?? null);
    }

    public function isLastNamedParameterVariadic(): bool
    {
        $lastNamedParameterIndex = $this->getLastNamedParameterIndex();

        return (
            array_key_exists($lastNamedParameterIndex, $this->getReflectionFunction()->getParameters())
            && $this->getReflectionFunction()->getParameters()[$lastNamedParameterIndex] instanceof \ReflectionParameter
            && $this->getReflectionFunction()->getParameters()[$lastNamedParameterIndex]->isVariadic()
        );
    }
}
