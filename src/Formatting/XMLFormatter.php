<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use DOMDocument;
use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\Exception\RuntimeException;
use Eboreum\Exceptional\ExceptionMessageGenerator;
use Eboreum\Exceptional\Factory\PHPCore\SimpleXMLElement\SimpleXMLElementFactory;
use Error;
use ReflectionMethod;
use ReflectionObject;
use SimpleXMLElement;
use Throwable;

use function assert;
use function date;
use function func_get_args;
use function htmlspecialchars;
use function is_int;
use function is_string;
use function sprintf;
use function strval;
use function trim;

use const ENT_COMPAT;
use const ENT_HTML401;

/**
 * {@inheritDoc}
 *
 * Formats a \Throwable to XML.
 */
class XMLFormatter extends AbstractXMLFormatter
{
    #[DebugIdentifier]
    protected CharacterEncoding $characterEncoding;

    protected ?SimpleXMLElementFactory $simpleXMLElementFactory = null;

    public function __construct(CasterInterface $caster, CharacterEncoding $characterEncoding)
    {
        $this->caster = $caster;
        $this->characterEncoding = $characterEncoding;
    }

    /**
     * {@inheritDoc}
     *
     * Returns XML.
     *
     * @throws RuntimeException
     */
    public function format(Throwable $throwable): string
    {
        try {
            $rootElementName = (
                $throwable instanceof Error
                ? 'error'
                : 'exception'
            );

            $simpleXMLElement = null;

            if ($this->getSimpleXMLElementFactory()) {
                $simpleXMLElement = $this->getSimpleXMLElementFactory()->createSimpleXMLElement($rootElementName);
            }

            if (null === $simpleXMLElement) {
                $simpleXMLElement = new SimpleXMLElement(sprintf(
                    '<?xml version="1.0" encoding="%s" ?><%s></%s>',
                    htmlspecialchars(
                        (string)$this->getCharacterEncoding(),
                        (ENT_COMPAT | ENT_HTML401),
                        (string)$this->getCharacterEncoding(),
                    ),
                    $rootElementName,
                    $rootElementName,
                ));
            }

            $simpleXMLElement = $this->formatInner($throwable, $simpleXMLElement);

            if ($this->isPrettyPrinting()) {
                $domDocument = new DOMDocument('1.0', (string)$this->getCharacterEncoding());
                $domDocument->preserveWhiteSpace = false;
                $domDocument->formatOutput = true;
                $xml = $simpleXMLElement->asXML();

                assert(is_string($xml));

                $domDocument->loadXML($xml);
                $xml = $domDocument->saveXML();
            } else {
                $xml = $simpleXMLElement->asXML();
            }

            assert(is_string($xml));
        } catch (Throwable $t) {
            throw new RuntimeException(ExceptionMessageGenerator::getInstance()->makeFailureInMethodMessage(
                $this,
                new ReflectionMethod($this, __FUNCTION__),
                func_get_args(),
            ), 0, $t);
        }

        return trim($xml);
    }

    /**
     * Returns a clone.
     */
    public function withSimpleXMLElementFactory(?SimpleXMLElementFactory $simpleXMLElementFactory): static
    {
        $clone = clone $this;
        $clone->simpleXMLElementFactory = $simpleXMLElementFactory;

        return $clone;
    }

    public function getCharacterEncoding(): CharacterEncoding
    {
        return $this->characterEncoding;
    }

    public function getSimpleXMLElementFactory(): ?SimpleXMLElementFactory
    {
        return $this->simpleXMLElementFactory;
    }

    protected function formatInner(Throwable $throwable, SimpleXMLElement $simpleXMLElement): SimpleXMLElement
    {
        $simpleXMLElement->addChild('class', Caster::makeNormalizedClassName(new ReflectionObject($throwable)));

        if ($this->isProvidingTimestamp()) {
            $simpleXMLElement->addChild('time', date('c'));
        }

        $simpleXMLElement->addChild('file', $this->normalizeFilePath($throwable->getFile()));
        $simpleXMLElement->addChild('line', strval($throwable->getLine()));
        $simpleXMLElement->addChild('code', strval($throwable->getCode()));
        $simpleXMLElement->addChild('message', $this->maskString($throwable->getMessage()));
        $simpleXMLElement->addChild('stacktrace', $this->maskString($throwable->getTraceAsString()));

        if ($throwable->getPrevious()) {
            $maximumPreviousDepth = $this->getMaximumPreviousDepth();
            $previousCount = $this->countPreviousThrowables($throwable);

            if (is_int($maximumPreviousDepth) && $this->getPreviousThrowableLevel() >= $maximumPreviousDepth) {
                $simpleXMLElement->addChild(
                    'previous',
                    sprintf(
                        '%d more (omitted)',
                        $previousCount,
                    ),
                );
            } else {
                $child = $this->withPreviousThrowableLevel($this->getPreviousThrowableLevel() + 1);

                $previous = $simpleXMLElement->addChild('previous');
                $child->formatInner($throwable->getPrevious(), $previous);
            }
        } else {
            $simpleXMLElement->addChild('previous');
        }

        return $simpleXMLElement;
    }
}
