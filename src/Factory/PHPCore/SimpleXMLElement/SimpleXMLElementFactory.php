<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Factory\PHPCore\SimpleXMLElement;

use Eboreum\Caster\CharacterEncoding;

/**
 * {@inheritDoc}
 */
class SimpleXMLElementFactory implements SimpleXMLElementFactoryInterface
{
    protected CharacterEncoding $characterEncoding;

    protected string $xmlVersion = '1.0';

    public function __construct(CharacterEncoding $characterEncoding)
    {
        $this->characterEncoding = $characterEncoding;
    }

    /**
     * {@inheritDoc}
     */
    public function createSimpleXMLElement(string $rootElementName): \SimpleXMLElement
    {
        return new \SimpleXMLElement(sprintf(
            '<?xml version="%s" encoding="%s" ?><%s></%s>',
            htmlspecialchars(
                (string)$this->getXMLVersion(),
                (ENT_COMPAT | ENT_HTML401),
                (string)$this->getCharacterEncoding(),
            ),
            htmlspecialchars(
                (string)$this->getCharacterEncoding(),
                (ENT_COMPAT | ENT_HTML401),
                (string)$this->getCharacterEncoding(),
            ),
            $rootElementName,
            $rootElementName,
        ));
    }

    /**
     * Returns a clone.
     */
    public function withCharacterEncoding(CharacterEncoding $characterEncoding): SimpleXMLElementFactory
    {
        $clone = clone $this;
        $clone->characterEncoding = $characterEncoding;

        return $clone;
    }

    /**
     * Returns a clone.
     */
    public function withXMLVersion(string $xmlVersion): SimpleXMLElementFactory
    {
        $clone = clone $this;
        $clone->xmlVersion = $xmlVersion;

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function getCharacterEncoding(): CharacterEncoding
    {
        return $this->characterEncoding;
    }

    /**
     * {@inheritDoc}
     */
    public function getXMLVersion(): string
    {
        return $this->xmlVersion;
    }
}