<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional;

use Eboreum\Exceptional\Caster;
use PHPUnit\Framework\TestCase;

class CasterTest extends TestCase
{
    public function testCreateInstanceWorks(): void
    {
        $casterA = Caster::create();
        $casterB = Caster::create();

        $this->assertNotSame($casterA, $casterB);
        $this->assertSame(Caster::class, get_class($casterA));
        $this->assertSame(Caster::class, get_class($casterB));
        $this->assertGreaterThan(0, count($casterA->getCustomObjectFormatterCollection()));
        $this->assertGreaterThan(0, count($casterB->getCustomObjectFormatterCollection()));
    }

    public function testGetInstanceWorks(): void
    {
        $casterA = Caster::getInstance();
        $casterB = Caster::getInstance();

        $this->assertSame($casterA, $casterB);
        $this->assertSame(Caster::class, get_class($casterA));
        $this->assertGreaterThan(0, count($casterA->getCustomObjectFormatterCollection()));
    }
}
