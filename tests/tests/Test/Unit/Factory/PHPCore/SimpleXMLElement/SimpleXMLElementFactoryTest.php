<?php

declare(strict_types = 1);

namespace Test\Unit\Eboreum\Exceptional\Factory\PHPCore\SimpleXMLElement;

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Exceptional\Factory\PHPCore\SimpleXMLElement\SimpleXMLElementFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SimpleXMLElementFactoryTest extends TestCase
{
    public function testBasics(): void
    {
        $characterEncoding = $this->_mockCharacterEncoding();

        $simpleXMLElementFactory = new SimpleXMLElementFactory($characterEncoding);

        $this->assertSame($characterEncoding, $simpleXMLElementFactory->getCharacterEncoding());
        $this->assertSame("1.0", $simpleXMLElementFactory->getXMLVersion());
    }

    public function testCreateSimpleXMLElementWorks(): void
    {
        $characterEncoding = $this->_mockCharacterEncoding();

        $characterEncoding
            ->expects($this->exactly(3))
            ->method("__toString")
            ->with()
            ->willReturn("UTF-8");

        $simpleXMLElementFactory = new SimpleXMLElementFactory($characterEncoding);

        $expected = implode("", [
            '<?xml version="1.0" encoding="UTF-8"?>',
            "\n<lorem/>",
            "\n",
        ]);

        $this->assertSame($expected, $simpleXMLElementFactory->createSimpleXMLElement("lorem")->asXML());
    }

    public function testWithCharacterEncodingWorks(): void
    {
        $characterEncodingA = $this->_mockCharacterEncoding();
        $simpleXMLElementFactoryA = new SimpleXMLElementFactory($characterEncodingA);
        $simpleXMLElementFactoryB = $simpleXMLElementFactoryA->withCharacterEncoding($characterEncodingA);

        $characterEncodingC = $this->_mockCharacterEncoding();
        $simpleXMLElementFactoryC = $simpleXMLElementFactoryA->withCharacterEncoding($characterEncodingC);

        $this->assertNotSame($simpleXMLElementFactoryA, $simpleXMLElementFactoryB);
        $this->assertNotSame($simpleXMLElementFactoryA, $simpleXMLElementFactoryC);
        $this->assertNotSame($simpleXMLElementFactoryB, $simpleXMLElementFactoryC);
        $this->assertSame($characterEncodingA, $simpleXMLElementFactoryA->getCharacterEncoding());
        $this->assertSame($characterEncodingA, $simpleXMLElementFactoryB->getCharacterEncoding());
        $this->assertSame($characterEncodingC, $simpleXMLElementFactoryC->getCharacterEncoding());
    }

    public function testWithXMLVersionWorks(): void
    {
        $characterEncoding = $this->_mockCharacterEncoding();
        $simpleXMLElementFactoryA = new SimpleXMLElementFactory($characterEncoding);
        $simpleXMLElementFactoryB = $simpleXMLElementFactoryA->withXMLVersion("1.0");

        $characterEncodingC = $this->_mockCharacterEncoding();
        $simpleXMLElementFactoryC = $simpleXMLElementFactoryA->withXMLVersion("2.0");

        $this->assertNotSame($simpleXMLElementFactoryA, $simpleXMLElementFactoryB);
        $this->assertNotSame($simpleXMLElementFactoryA, $simpleXMLElementFactoryC);
        $this->assertNotSame($simpleXMLElementFactoryB, $simpleXMLElementFactoryC);
        $this->assertSame("1.0", $simpleXMLElementFactoryA->getXMLVersion());
        $this->assertSame("1.0", $simpleXMLElementFactoryB->getXMLVersion());
        $this->assertSame("2.0", $simpleXMLElementFactoryC->getXMLVersion());
    }

    /**
     * @return CharacterEncoding&MockObject
     */
    private function _mockCharacterEncoding(): CharacterEncoding
    {
        return $this
            ->getMockBuilder(CharacterEncoding::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}