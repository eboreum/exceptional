<?php

declare(strict_types = 1); // README.md.remove

use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\DefaultFormatter;

require_once dirname(__DIR__, 3) . "/bootstrap.php"; // README.md.remove

$caster = Caster::getInstance();
$defaultFormatter = new DefaultFormatter($caster);

$throwable = new \Exception("foo");

$result = $defaultFormatter->format($throwable);

$result = preg_replace('/\nStacktrace:(\n(    #\d+).+)+\nPrevious:/', "\nStacktrace:\n    #0 /path/to/some/file.php:34: fake_function()\nPrevious:", $result); // README.md.remove
$filePathToRemove = str_replace("\\", "/", PROJECT_ROOT_DIRECTORY_PATH) . "/"; // README.md.remove
$result = str_replace($filePathToRemove, "/some/file/path/", $result); // README.md.remove

echo $result;