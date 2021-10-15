<?php

declare(strict_types=1);

namespace Eboreum\Exceptional;

use Eboreum\Caster\Caster as OriginalCaster;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\CharacterEncodingInterface;
use Eboreum\Caster\Formatter\Object_\ClosureFormatter;
use Eboreum\Caster\Formatter\Object_\DateTimeInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\DebugIdentifierAnnotationInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\DirectoryFormatter;
use Eboreum\Caster\Formatter\Object_\TextuallyIdentifiableInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\ThrowableFormatter;

/**
 * {@inheritDoc}
 */
class Caster extends OriginalCaster
{
    private static ?Caster $instance = null;

    /**
     * {@inheritDoc}
     */
    public static function create(?CharacterEncodingInterface $characterEncoding = null): Caster
    {
        if (null === $characterEncoding) {
            $characterEncoding = CharacterEncoding::getInstance();
        }

        $caster = new self($characterEncoding);

        return $caster->withCustomObjectFormatterCollection(new ObjectFormatterCollection(...[
            new TextuallyIdentifiableInterfaceFormatter(),
            new DebugIdentifierAnnotationInterfaceFormatter(),
            new ClosureFormatter(),
            new DirectoryFormatter(),
            new DateTimeInterfaceFormatter(),
            new ThrowableFormatter(),
        ]));
        // @phpstan-ignore-line
    }

    public static function getInstance(): Caster
    {
        if (null === self::$instance) {
            self::$instance = self::create();
        }

        return self::$instance;
    }
}
