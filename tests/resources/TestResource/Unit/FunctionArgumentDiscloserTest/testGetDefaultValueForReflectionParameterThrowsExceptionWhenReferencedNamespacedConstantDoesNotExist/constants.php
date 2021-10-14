<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\Exceptional\FunctionArgumentDiscloserTest\testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist;

define(
    "EBOREUM_EXCEPTIONAL_TEST_3ae1cc1de032441d9a2ac7929b9d9892",
    sprintf(
        "A global constant utilized in unit test %s\\FunctionArgumentDiscloserTest->testBasics",
        __NAMESPACE__,
    ),
);

define(
    __NAMESPACE__ . "\\EBOREUM_EXCEPTIONAL_TEST_e000d6a7ba5941278d823905f218b71f",
    sprintf(
        "A namespaced constant utilized in unit test %s\\FunctionArgumentDiscloserTest->testBasics",
        __NAMESPACE__,
    ),
);