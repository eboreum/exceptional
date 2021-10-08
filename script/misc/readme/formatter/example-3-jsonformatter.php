<?php

declare(strict_types = 1); // README.md.remove

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Formatting\JSONFormatter;

require_once dirname(__DIR__, 3) . "/bootstrap.php"; // README.md.remove

$caster = Caster::getInstance();
$characterEncoding = new CharacterEncoding("UTF-8");
$jsonFormatter = new JSONFormatter($caster, $characterEncoding);
$jsonFormatter = $jsonFormatter->withFlags(JSON_PRETTY_PRINT);

$throwable = new \Exception("foo");

$result = $jsonFormatter->format($throwable);

$result = preg_replace('/(\n +"stacktrace"\:) "#0 .+",/', '$1 "#0 \\/path\\/to\\/some\\/file.php:34: fake_function()"', $result); // README.md.remove
$filePathToRemove = str_replace("\\", "/", PROJECT_ROOT_DIRECTORY_PATH) . "/"; // README.md.remove
$result = str_replace(mb_substr(json_encode($filePathToRemove), 1, -1, "UTF-8"), mb_substr(json_encode("/some/file/path/"), 1, -1, "UTF-8"), $result); // README.md.remove

echo $result;