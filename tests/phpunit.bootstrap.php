<?php

declare(strict_types=1);

require(dirname(__DIR__) . '/script/bootstrap.php');

define('TEST_ROOT_PATH', __DIR__);

ini_set('memory_limit', '1024M');

// Report only certain error. Leave out E_DEPRECATED on purpose, since certain tests are purposely targeting deprecated features
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once TEST_ROOT_PATH . '/resources/TestResource/Unit/FunctionArgumentDiscloserTest/testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist/constants.php';
require_once TEST_ROOT_PATH . '/resources/TestResource/Unit/FunctionArgumentDiscloserTest/testGetDefaultValueForReflectionParameterThrowsExceptionWhenReferencedNamespacedConstantDoesNotExist/functions.php';