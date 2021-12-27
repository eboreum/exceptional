<?php

declare(strict_types = 1); // README.md.remove

use Eboreum\Caster\Annotation\DebugIdentifier;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Caster\Contract\TextuallyIdentifiableInterface;
use Eboreum\Caster\Contract\DebugIdentifierAnnotationInterface;
use Eboreum\Caster\Formatter\Object_\DebugIdentifierAnnotationInterfaceFormatter;
use Eboreum\Caster\Formatter\Object_\TextuallyIdentifiableInterfaceFormatter;
use Eboreum\Exceptional\Caster;
use Eboreum\Exceptional\ExceptionMessageGenerator;

require_once dirname(__DIR__, 3) . "/bootstrap.php"; // README.md.remove

// Using TextuallyIdentifiableInterface

class Foo1990801ff8324df1b73e323d7fca71a8 implements TextuallyIdentifiableInterface
{
    protected int $id = 42;

    /**
     * @throws \RuntimeException
     */
    public function bar(int $a): string
    {
        $caster = Caster::getInstance();
        $caster = $caster->withCustomObjectFormatterCollection(new ObjectFormatterCollection(...[
            new TextuallyIdentifiableInterfaceFormatter(),
        ]));


        $exceptionMessageGenerator = ExceptionMessageGenerator::getInstance()->withCaster($caster);

        throw new \RuntimeException($exceptionMessageGenerator->makeFailureInMethodMessage(
            $this,
            new \ReflectionMethod(__CLASS__, __FUNCTION__),
            func_get_args(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function toTextualIdentifier(CasterInterface $caster): string
    {
        return sprintf(
            "My ID is: %d",
            $this->id,
        );
    }
};


$foo = new Foo1990801ff8324df1b73e323d7fca71a8;

try {
    $foo->bar(7);
} catch (\RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}

/**
 * Using DebugIdentifierAnnotationInterface
 *
 * Requires: https://packagist.org/packages/doctrine/annotations
 */

class Foo31eda25b57e8456fb2b3e8158232b5e5 implements DebugIdentifierAnnotationInterface
{
    /**
     * @DebugIdentifier
     */
    protected int $id = 42;

    /**
     * @throws \RuntimeException
     */
    public function bar(int $a): string
    {
        $caster = Caster::getInstance();
        $caster = $caster->withCustomObjectFormatterCollection(new ObjectFormatterCollection(...[
            new DebugIdentifierAnnotationInterfaceFormatter(),
        ]));

        $exceptionMessageGenerator = ExceptionMessageGenerator::getInstance()->withCaster($caster);

        throw new \RuntimeException($exceptionMessageGenerator->makeFailureInMethodMessage(
            $this,
            new \ReflectionMethod(__CLASS__, __FUNCTION__),
            func_get_args(),
        ));
    }
};


$foo = new Foo31eda25b57e8456fb2b3e8158232b5e5;

try {
    $foo->bar(7);
} catch (\RuntimeException $e) {
    echo $e->getMessage() . PHP_EOL;
}