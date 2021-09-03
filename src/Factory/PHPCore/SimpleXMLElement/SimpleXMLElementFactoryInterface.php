<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Factory\PHPCore\SimpleXMLElement;

use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\ImmutableObjectInterface;

/**
 * {@inheritDoc}
 */
interface SimpleXMLElementFactoryInterface extends ImmutableObjectInterface
{
    /**
     * @param string $rootElementName           A name for the outermost element in the XML tree.
     */
    public function createSimpleXMLElement(string $rootElementName): \SimpleXMLElement;

    /**
     * The character encoding utilized within the XML tree.
     */
    public function getCharacterEncoding(): CharacterEncoding;

    /**
     * The XML version to utilize, e.g. "1.0".
     */
    public function getXMLVersion(): string;
}