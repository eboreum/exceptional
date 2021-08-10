<?php

declare(strict_types=1);

namespace Eboreum\Exceptional\Formatting;

use Eboreum\Caster\Annotation\DebugIdentifier;
use Eboreum\Caster\CharacterEncoding;
use Eboreum\Caster\Contract\CasterInterface;
use Eboreum\Exceptional\Caster;

/**
 * {@inheritDoc}
 *
 * Formats a \Throwable to HTML5.
 */
class HTML5TableFormatter extends AbstractXMLFormatter
{
    /**
     * @DebugIdentifier
     */
    protected CharacterEncoding $characterEncoding;

    public function __construct(CasterInterface $caster, CharacterEncoding $characterEncoding)
    {
        $this->caster = $caster;
        $this->characterEncoding = $characterEncoding;
    }

    /**
     * {@inheritDoc}
     *
     * Returns a HTML5 string with values having been properly escaped.
     */
    public function format(\Throwable $throwable): string
    {
        $normalizedClassName = Caster::makeNormalizedClassName(new \ReflectionObject($throwable));

        $headingHTML = sprintf(
            '<h1>%s</h1>',
            $this->htmlEncodeWithLn2Br($normalizedClassName),
        );

        $trs = [];

        if ($this->isProvidingTimestamp()) {
            $trs[] = [
                $this->htmlEncode("Time:"),
                $this->htmlEncode(date("c")),
            ];
        }

        $trs[] = [
            $this->htmlEncode("Message:"),
            $this->htmlEncodeWithLn2Br($this->maskString($throwable->getMessage())),
        ];
        $trs[] = [
            $this->htmlEncode("File:"),
            $this->htmlEncodeWithLn2Br($this->normalizeFilePath($throwable->getFile())),
        ];
        $trs[] = [
            $this->htmlEncode("Line:"),
            $this->htmlEncodeWithLn2Br(strval($throwable->getLine())),
        ];
        $trs[] = [
            $this->htmlEncode("Code:"),
            $this->htmlEncodeWithLn2Br(strval($throwable->getCode())),
        ];
        $trs[] = [
            $this->htmlEncode("Stacktrace:"),
            '<pre>' . $this->htmlEncodeWithLn2Br($this->maskString($throwable->getTraceAsString())) . '</pre>',
        ];

        if ($throwable->getPrevious()) {
            $maximumPreviousDepth = $this->getMaximumPreviousDepth();
            $previousCount = $this->countPreviousThrowables($throwable);

            if (is_int($maximumPreviousDepth) && $this->getPreviousThrowableLevel() >= $maximumPreviousDepth) {
                $trs[] = [
                    $this->htmlEncode("Previous:"),
                    $this->htmlEncode(sprintf(
                        "(%d more) (omitted)",
                        $previousCount,
                    )),
                ];
            } else {
                $child = $this->withPreviousThrowableLevel($this->getPreviousThrowableLevel() + 1);

                $trs[] = [
                    $this->htmlEncode("Previous:"),
                    $this->htmlEncode(sprintf(
                        "(%d more)",
                        $previousCount,
                    )) . $child->format($throwable->getPrevious()),
                ];
            }
        } else {
            $trs[] = [
                $this->htmlEncode("Previous:"),
                $this->htmlEncode("(None)"),
            ];
        }

        $html = '<table><tbody>';

        $html .= '<tr><td colspan="2">' . $headingHTML . '</td></tr>';

        foreach ($trs as $tr) {
            $html .= '<tr>';

            foreach ($tr as $td) {
                $html .= '<td>' . $td . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        if ($this->isPrettyPrinting()) {
            $domDocument = new \DOMDocument("1.0", (string)$this->getCharacterEncoding());
            $domDocument->preserveWhiteSpace = false;
            $domDocument->formatOutput = true;
            $domDocument->loadHTML($html);

            $tables = $domDocument->getElementsByTagName("table");

            assert($tables instanceof \DOMNodeList);

            $table = $tables->item(0);

            assert(is_object($table));
            assert($table instanceof \DOMElement);
            assert($table->nodeName === "table");

            $html = $domDocument->saveXML($table);

            assert(is_string($html));

            return $html;
        }

        return $html;
    }

    public function htmlEncode(string $text): string
    {
        return htmlspecialchars(
            $text,
            ENT_COMPAT | ENT_HTML5,
            (string)$this->getCharacterEncoding(),
            true,
        );
    }

    public function htmlEncodeWithLn2Br(string $text): string
    {
        $lines = static::splitTextLinesToArray($text);

        array_walk($lines, function(string &$line){
            $line = $this->htmlEncode($line);
        });

        return implode('<br>', $lines);
    }

    public function withIsPrettyPrinting(bool $isPrettyPrinting): HTML5TableFormatter
    {
        return parent::withIsPrettyPrinting($isPrettyPrinting); /** @phpstan-ignore-line */
    }

    public function getCharacterEncoding(): CharacterEncoding
    {
        return $this->characterEncoding;
    }
}
