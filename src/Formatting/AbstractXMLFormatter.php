<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Annotation\DebugIdentifier;

/**
 * {@inheritDoc}
 */
abstract class AbstractXMLFormatter extends AbstractFormatter
{
    /** @DebugIdentifier */
    protected bool $isPrettyPrinting = false;

    /**
     * Returns a clone.
     */
    public function withIsPrettyPrinting(bool $isPrettyPrinting): AbstractXMLFormatter
    {
        $clone = clone $this;
        $clone->isPrettyPrinting = $isPrettyPrinting;

        return $clone;
    }

    public function isPrettyPrinting(): bool
    {
        return $this->isPrettyPrinting;
    }
}
