<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\MethodArgumentDiscloserTest\testBasics;

use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\MethodArgumentDiscloser;

class PrivateConstantReferencedAsDefaultInParameter
{
    private const BAR = 42;

    /**
     * @return array{\ReflectionMethod, array<int, mixed>, MethodArgumentDiscloser}
     */
    public function foo(int $a = PrivateConstantReferencedAsDefaultInParameter::BAR): array
    {
        $reflectionMethod = new \ReflectionMethod($this, __FUNCTION__);
        $methodArgumentValues = func_get_args();

        return [
            $reflectionMethod,
            $methodArgumentValues,
            new MethodArgumentDiscloser(
                Caster::getInstance(),
                $reflectionMethod,
                $methodArgumentValues
            ),
        ];
    }
}