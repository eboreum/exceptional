<?php

declare(strict_types=1);

namespace Eboreum\Exceptional;

use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Contract\ImmutableObjectInterface;
use Eboreum\Exceptional\Exception\RuntimeException;

class ExceptionMessageGenerator implements ImmutableObjectInterface
{
    protected CasterInterface $caster;

    private static ?ExceptionMessageGenerator $instance = null;

    public function __construct(CasterInterface $caster)
    {
        $this->caster = $caster;
    }

    public static function getInstance(): ExceptionMessageGenerator
    {
        if (null === self::$instance) {
            self::$instance = new self(Caster::getInstance());
        }

        return self::$instance;
    }

    /**
     * A proxy/convenience method for `MethodArgumentsToString->cast()`.
     *
     * Takes a \ReflectionMethod and an array of values from the arguments in the method, produced by `func_get_args()`,
     * and produces and returns a string, where the method's arguments are named and attached to their respective
     * values.
     *
     * As this method utilizes the Reflection API (https://www.php.net/manual/en/book.reflection.php), which is slow,
     * this method should mainly be used in failure scenarios, e.g. as part of building an exception message.
     *
     * Example:
     *
     *     public function foo(string $a, int $b, ?bool $c) { ... }
     *
     * When this is called with:
     *
     *     $obj->foo("bar", 42, null);
     *
     * Then calling:
     *
     *     MethodArgumentsToStrings->cast(new \ReflectionMethod($this, __FUNCTION__), func_get_args());
     *
     * Will produce the string:
     *
     *     $a = (string(3)) "foo", $b = (int) 42, $c = (null) null
     *
     * If a method is called with arguments, these will be indexed and shown as "{#}", e.g. "{0}" for the first
     * argument.
     *
     * @param array<int, mixed> $functionArgumentValues
     *                                          As produced by `func_get_args()` inside the function/method referred to
     *                                          in the $reflectionFunction argument.
     * @throws RuntimeException
     */
    public function castFunctionArgumentsToString(
        \ReflectionFunctionAbstract $reflectionFunction,
        array $functionArgumentValues
    ): string {
        try {
            $casterInner = $this->getCaster()->withDepthCurrent(
                $this->getCaster()->getDepthCurrent()
            );

            $discloser = null;

            if ($reflectionFunction instanceof \ReflectionFunction) {
                $discloser = new FunctionArgumentDiscloser(
                    $casterInner,
                    $reflectionFunction,
                    $functionArgumentValues
                );
            } elseif ($reflectionFunction instanceof \ReflectionMethod) {
                $discloser = new MethodArgumentDiscloser(
                    $casterInner,
                    $reflectionFunction,
                    $functionArgumentValues
                );
            }

            if (null === $discloser) {
                throw new RuntimeException(sprintf(
                    'A $discloser was not produced from $reflectionFunction = %s',
                    Caster::getInstance()->castTyped($reflectionFunction),
                ));
            }

            $normalizedMethodArgumentValues = $discloser->getNormalizedFunctionArgumentValues();
            $lastParameterIndex = $discloser->getLastNamedParameterIndex();
            $isLastNamedParameterVariadic = $discloser->isLastNamedParameterVariadic();
            $normalizedMethodArgumentValuesCount = $discloser->getNormalizedFunctionArgumentValuesCount();

            $argumentsAsStrings = [];
            $index = -1;

            $handledParameterCount = 0;

            foreach ($reflectionFunction->getParameters() as $reflectionParameter) {
                $index++;

                $isAtLastNamedParameterAndLastParameterIsVariadic = (
                    $isLastNamedParameterVariadic
                    && $lastParameterIndex === $index
                );

                if ($isAtLastNamedParameterAndLastParameterIsVariadic) {
                    $argumentsAsStrings[] = sprintf(
                        '$%s = ...%s',
                        $reflectionParameter->getName(),
                        $casterInner->castTyped($normalizedMethodArgumentValues[$index] ?? []),
                    );
                } else {
                    $argumentsAsStrings[] = sprintf(
                        '$%s = %s',
                        $reflectionParameter->getName(),
                        $casterInner->castTyped($normalizedMethodArgumentValues[$index] ?? null),
                    );
                }

                $handledParameterCount++;
            }

            if ($normalizedMethodArgumentValuesCount > $handledParameterCount) {
                /**
                 * Handle that PHP allows that callers provide additional, non-named arguments when a method (or
                 * function) is called. This may be used with userland implementations such as using `func_get_args()`
                 * to extract the arguments.
                 */

                $firstArgumentIndex = $handledParameterCount;
                $lastArgumentIndex = $normalizedMethodArgumentValuesCount - 1;

                for ($argumentIndex = $firstArgumentIndex; $argumentIndex <= $lastArgumentIndex; $argumentIndex++) {
                    $argumentsAsStrings[] = sprintf(
                        '{%d} = %s',
                        $argumentIndex,
                        $casterInner->castTyped(
                            $normalizedMethodArgumentValues[$argumentIndex]
                        ),
                    );
                }
            }

            $str = implode(', ', $argumentsAsStrings);
        } catch (\Throwable $t) {
            $declaringClassText = '';

            if ($reflectionFunction instanceof \ReflectionMethod) {
                $declaringClassText = sprintf(
                    ' (declaring class: %s)',
                    Caster::makeNormalizedClassName($reflectionFunction->getDeclaringClass()),
                );
            }

            throw new RuntimeException(sprintf(
                implode('', [
                    'Failure in \\%s->%s(',
                        '$reflectionFunction = (object) %s%s',
                        ', $functionArgumentValues = %s',
                    ')',
                ]),
                static::class,
                __FUNCTION__,
                Caster::makeNormalizedClassName(new \ReflectionObject($reflectionFunction)),
                $declaringClassText,
                $this->getCaster()->castTyped($functionArgumentValues),
            ), 0, $t);
        }

        return $str;
    }

    /**
     * Creates an exception message on a generic format, explaining that a failure has occurred within a function, and
     * also provides a list of the arguments, which has been passed to the respective function.
     *
     * As this method utilizes the Reflection API (https://www.php.net/manual/en/book.reflection.php), which is slow,
     * this method should mainly be used in failure scenarios, e.g. as part of building an exception message.
     *
     * @param array<int, mixed> $functionArgumentValues
     * @throws RuntimeException
     */
    public function makeFailureInFunctionMessage(
        \ReflectionFunction $reflectionFunction,
        array $functionArgumentValues
    ): string {
        $output = null;

        try {
            if ($reflectionFunction->isClosure()) {
                $argumentCount = count($functionArgumentValues);
                $output = sprintf(
                    implode('', [
                        'Failure in closure/anonymous function defined in %s:%d, called with %d %s and actually',
                        ' having arguments: (%s)',
                    ]),
                    $reflectionFunction->getFileName(),
                    $reflectionFunction->getStartLine(),
                    $argumentCount,
                    (
                        1 === $argumentCount
                        ? 'argument'
                        : 'arguments'
                    ),
                    $this->castFunctionArgumentsToString($reflectionFunction, $functionArgumentValues),
                );
            }

            if (!$output) {
                $output = sprintf(
                    'Failure in function \\%s(%s)',
                    $reflectionFunction->getName(),
                    $this->castFunctionArgumentsToString($reflectionFunction, $functionArgumentValues),
                );
            }
        } catch (\Throwable $t) {
            $argumentsAsStrings = [];
            $argumentsAsStrings[] = sprintf(
                '$reflectionFunction = %s',
                $this->getCaster()->castTyped($reflectionFunction),
            );
            $argumentsAsStrings[] = sprintf(
                '$functionArgumentValues = %s',
                $this->getCaster()->castTyped($functionArgumentValues),
            );

            throw new RuntimeException(sprintf(
                'Failure in \\%s->%s(%s)',
                static::class,
                __FUNCTION__,
                implode(', ', $argumentsAsStrings),
            ), 0, $t);
        }

        return $output;
    }

    /**
     * Creates an exception message on a generic format, explaining that a failure has occurred within a given class and
     * method, and also provides a list of the arguments, which has been passed to the respective method.
     *
     * As this method utilizes the Reflection API (https://www.php.net/manual/en/book.reflection.php), which is slow,
     * this method should mainly be used in failure scenarios, e.g. as part of building an exception message.
     *
     * @param object|class-string $objectOrClassName
     *                                          Must be an object instance or a valid class name. For static methods,
     *                                          you may use `get_called_class()`.
     * @param array<int, mixed> $methodArgumentValues
     * @throws RuntimeException
     */
    public function makeFailureInMethodMessage(
        $objectOrClassName,
        \ReflectionMethod $reflectionMethod,
        array $methodArgumentValues
    ): string {
        try {
            $errorMessages = [];
            $className = '';

            if (is_object($objectOrClassName)) {
                $reflectionObject = new \ReflectionObject($objectOrClassName);
                $className = Caster::makeNormalizedClassName($reflectionObject);

                $isClassAcceptable = (
                    $reflectionObject->getName() === $reflectionMethod->getDeclaringClass()->getName()
                );

                if (false === $isClassAcceptable) {
                    $isClassAcceptable = is_subclass_of(
                        $reflectionObject->getName(),
                        $reflectionMethod->getDeclaringClass()->getName(),
                        true,
                    );
                }

                if (false === $isClassAcceptable) { // @phpstan-ignore-line
                    $errorMessages[] = sprintf(
                        implode('', [
                            'Arguments $objectOrClassName = %s and $reflectionMethod = %s (declaring class name:',
                            ' %s) are problematic as they do not do not reference the same class or a child',
                            ' class hereof',
                        ]),
                        $this->getCaster()->castTyped($objectOrClassName),
                        $this->getCaster()->castTyped($reflectionMethod),
                        Caster::makeNormalizedClassName($reflectionMethod->getDeclaringClass()),
                    );
                }
            } elseif (is_string($objectOrClassName)) {
                if (class_exists($objectOrClassName)) {
                    $isClassAcceptable = (
                        $objectOrClassName === $reflectionMethod->getDeclaringClass()->getName()
                    );

                    if (false === $isClassAcceptable) {
                        $isClassAcceptable = is_subclass_of(
                            $objectOrClassName,
                            $reflectionMethod->getDeclaringClass()->getName(),
                            true,
                        );
                    }

                    if (false === $isClassAcceptable) { // @phpstan-ignore-line
                        $errorMessages[] = sprintf(
                            implode('', [
                                'Arguments $objectOrClassName = %s and $reflectionMethod = %s (declaring class',
                                ' name: %s) are problematic as they do not do not reference the same class or a',
                                ' child class hereof',
                            ]),
                            $this->getCaster()->castTyped($objectOrClassName),
                            $this->getCaster()->castTyped($reflectionMethod),
                            Caster::makeNormalizedClassName($reflectionMethod->getDeclaringClass()),
                        );
                    }
                } else {
                    $errorMessages[] = sprintf(
                        'Argument $objectOrClassName = %s refers to a non-existing class',
                        $this->getCaster()->castTyped($objectOrClassName),
                    );
                }

                if (!$errorMessages) {
                    $reflectionClass = new \ReflectionClass($objectOrClassName);
                    $className = Caster::makeNormalizedClassName($reflectionClass);
                }
            } else {
                $errorMessages[] = sprintf(
                    'Expects argument $objectOrClassName to be an object or a string, but it is not. Found: %s',
                    $this->getCaster()->castTyped($objectOrClassName),
                );
            }

            if ($errorMessages) {
                throw new RuntimeException(implode('. ', $errorMessages));
            }

            $output = sprintf(
                'Failure in %s%s%s(%s)',
                $className,
                (
                    $reflectionMethod->isStatic()
                    ? '::'
                    : '->'
                ),
                $reflectionMethod->getName(),
                $this->castFunctionArgumentsToString($reflectionMethod, $methodArgumentValues),
            );

            if (is_object($objectOrClassName)) {
                $output .= ' inside ' . $this->getCaster()->castTyped($objectOrClassName);
            }
        } catch (\Throwable $t) {
            $argumentsAsStrings = [];
            $argumentsAsStrings[] = sprintf(
                '$objectOrClassName = %s',
                $this->getCaster()->castTyped($objectOrClassName),
            );
            $argumentsAsStrings[] = sprintf(
                '$reflectionMethod = %s',
                $this->getCaster()->castTyped($reflectionMethod),
            );
            $argumentsAsStrings[] = sprintf(
                '$methodArgumentValues = %s',
                $this->getCaster()->castTyped($methodArgumentValues),
            );

            throw new RuntimeException(sprintf(
                'Failure in \\%s->%s(%s)',
                static::class,
                __FUNCTION__,
                implode(', ', $argumentsAsStrings),
            ), 0, $t);
        }

        return $output;
    }

    /**
     * Creates a list of named class properties and their values. Additionally, will gracefully handle displaying of
     * uninitialized class properties.
     *
     * As this method utilizes the Reflection API (https://www.php.net/manual/en/book.reflection.php), which is slow,
     * this method should mainly be used in failure scenarios, e.g. as part of building an exception message.
     *
     * @param array<string> $propertyNamesToBeShown
     *                                          The property names in this array must exist on the class from the
     *                                          provided $object or one of its parent classes. Otherwise, an exception
     *                                          is thrown.
     * @throws RuntimeException
     */
    public function makeUninitializedPropertySafeToTextualIdentifierString(
        object $object,
        array $propertyNamesToBeShown
    ): string {
        try {
            $reflectionObject = new \ReflectionObject($object);
            $errorMessages = [];

            $invalids = [];
            $propertyNameToReflectionPropertyMap = [];

            foreach ($propertyNamesToBeShown as $i => $propertyName) {
                if (false === is_string($propertyName)) { // @phpstan-ignore-line
                    $invalids[] = sprintf(
                        'Element is not a string: %s => %s',
                        $this->getCaster()->cast($i),
                        $this->getCaster()->castTyped($propertyName),
                    );
                } else {
                    $reflectionClassCurrent = $reflectionObject;

                    $hasProperty = false;
                    $classHierarchyIndex = -1;

                    while ($reflectionClassCurrent) {
                        $classHierarchyIndex++;

                        if ($reflectionClassCurrent->hasProperty($propertyName)) {
                            $hasProperty = true;
                            $reflectionProperty = $reflectionClassCurrent->getProperty($propertyName);
                            $propertyNameToReflectionPropertyMap[$propertyName] = $reflectionProperty;

                            break;
                        }

                        $reflectionClassCurrent = $reflectionClassCurrent->getParentClass();
                    }

                    if (false === $hasProperty) {
                        $invalids[] = sprintf(
                            'Property %s does not exist on class %s%s',
                            $this->getCaster()->cast($propertyName),
                            Caster::makeNormalizedClassName($reflectionObject),
                            (
                                $classHierarchyIndex > 0
                                ? ' or any of its parent classes'
                                : ''
                            ),
                        );
                    }
                }
            }

            if ($invalids) {
                $errorMessages[] = sprintf(
                    'In argument $propertyNamesToBeShown, %d/%d elements are invalid, including: %s',
                    count($invalids),
                    count($propertyNamesToBeShown),
                    implode(',', $invalids),
                );
            }

            if ($errorMessages) {
                throw new RuntimeException(implode('. ', $errorMessages));
            }

            $output = Caster::makeNormalizedClassName($reflectionObject);

            $propertiesStringified = [];

            foreach ($propertyNameToReflectionPropertyMap as $propertyName => $reflectionProperty) {
                $reflectionProperty->setAccessible(true);

                if ($reflectionProperty->isInitialized($object)) {
                    $propertiesStringified[] = sprintf(
                        '$%s = %s',
                        $propertyName,
                        $this->getCaster()->castTyped($reflectionProperty->getValue($object)),
                    );
                } else {
                    $propertiesStringified[] = sprintf(
                        '$%s = (uninitialized)',
                        $propertyName,
                    );
                }
            }

            if ($propertiesStringified) {
                $output .= sprintf(
                    ' {%s}',
                    implode(', ', $propertiesStringified),
                );
            }
        } catch (\Throwable $t) {
            $argumentSegments = [];
            $argumentSegments[] = sprintf(
                '$object = %s',
                $this->getCaster()->castTyped($object),
            );
            $argumentSegments[] = sprintf(
                '$propertyNamesToBeShown = %s',
                $this->getCaster()->castTyped($propertyNamesToBeShown),
            );

            throw new RuntimeException(sprintf(
                'Failure in \\%s->%s(%s)',
                static::class,
                __FUNCTION__,
                implode(', ', $argumentSegments),
            ), 0, $t);
        }

        return $output;
    }

    /**
     * Returns a clone.
     */
    public function withCaster(CasterInterface $caster): ExceptionMessageGenerator
    {
        $clone = clone $this;
        $clone->caster = $caster;

        return $clone;
    }

    public function getCaster(): CasterInterface
    {
        return $this->caster;
    }
}
