<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist; // phpcs:ignore

use function define;
use function sprintf;

define(
    'EBOREUM_EXCEPTIONAL_TEST_3AE1CC1DE032441D9A2AC7929B9D9892',
    sprintf(
        'A global constant utilized in unit test %s\\FunctionArgumentDiscloserTest->testBasics',
        __NAMESPACE__,
    ),
);

define(
    __NAMESPACE__ . '\\EBOREUM_EXCEPTIONAL_TEST_E000D6A7BA5941278D823905F218B71F',
    sprintf(
        'A namespaced constant utilized in unit test %s\\FunctionArgumentDiscloserTest->testBasics',
        __NAMESPACE__,
    ),
);
