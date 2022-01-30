<?php

declare(strict_types = 1); // README.md.remove

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\OnelineFormatter;

require_once dirname(__DIR__, 3) . '/bootstrap.php'; // README.md.remove

$caster = Caster::getInstance();
$onelineFormatter = new OnelineFormatter($caster);

$throwable = new \Exception('foo');

$result = $onelineFormatter->format($throwable);

$result = preg_replace('/ Stacktrace: (#\d+ .+)+ Previous:/', ' Stacktrace: #0 /path/to/some/file.php:34: fake_function(). Previous:', $result); // README.md.remove
assert(is_string($result)); // README.md.remove
$filePathToRemove = str_replace('\\', '/', PROJECT_ROOT_DIRECTORY_PATH) . '/'; // README.md.remove
$result = str_replace($filePathToRemove, '/some/file/path/', $result); // README.md.remove

echo $result;