<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\Diff\Differ;

class ReadmeMdTest extends TestCase
{
    private string $contents;

    public function setUp(): void
    {
        $readmeFilePath = dirname(TEST_ROOT_PATH) . "/README.md";

        $this->assertTrue(is_file($readmeFilePath), "README.md does not exist!");

        $contents = file_get_contents($readmeFilePath);

        $this->assertIsString($contents);

        $this->contents = $contents;
    }

    /**
     * Did we leave remember to update the contents of README.md?
     *
     * @throws \RuntimeException
     */
    public function testCompareContents(): void
    {
        ob_start();
        include dirname(TEST_ROOT_PATH) . "/script/make-readme.php";
        $producedContents = ob_get_contents();
        ob_end_clean();

        assert(is_string($producedContents));

        if ($this->contents !== $producedContents) {
            $differ = new Differ;

            throw new \RuntimeException(sprintf(
                implode("", [
                    "README.md is not up–to-date. Please run: php script/make-readme.php.",
                    " The diff is:\n\n%s",
                ]),
                $differ->diff($this->contents, $producedContents),
            ));
        }

        $this->assertTrue(true);
    }

    public function testDoesReadmeMdContainLocalFilePaths(): void
    {
        $rootPath = dirname(TEST_ROOT_PATH);

        $split = preg_split('/([\\\\\/])/', $rootPath);

        $this->assertIsArray($split);

        $rootPathRegex = sprintf(
            '/%s/',
            implode(
                '(\\\\+\/|\\\\+|\/)', // Handle both Windows and Unix
                array_map(
                    function(string $v){
                        return preg_quote($v, "/");
                    },
                    $split,
                ),
            ),
        );

        $this->assertSame(
            0,
            preg_match($rootPathRegex, $this->contents),
            "README.md contains local file paths (on your system) and it should not.",
        );
    }
}
