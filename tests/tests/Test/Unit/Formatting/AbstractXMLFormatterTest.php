<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\Exceptional\Formatting;

use Eboreum\Exceptional\Formatting\AbstractXMLFormatter;
use PHPUnit\Framework\TestCase;
use Throwable;

class AbstractXMLFormatterTest extends TestCase
{
    public function testBasics(): void
    {
        $object = new class extends AbstractXMLFormatter
        {
            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $this->assertSame(false, $object->isPrettyPrinting());
    }

    public function testWithIsPrettyPrintingWorks(): void
    {
        $objectA = new class extends AbstractXMLFormatter
        {
            public function format(Throwable $throwable): string
            {
                return '';
            }
        };

        $objectB = $objectA->withIsPrettyPrinting(false);
        $objectC = $objectA->withIsPrettyPrinting(true);

        $this->assertNotSame($objectA, $objectB);
        $this->assertNotSame($objectA, $objectC);
        $this->assertNotSame($objectB, $objectC);
        $this->assertSame(false, $objectA->isPrettyPrinting());
        $this->assertSame(false, $objectB->isPrettyPrinting());
        $this->assertSame(true, $objectC->isPrettyPrinting());
    }
}
