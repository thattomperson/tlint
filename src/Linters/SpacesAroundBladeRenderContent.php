<?php

namespace Tighten\Linters;

use Illuminate\Support\Str;
use PhpParser\Parser;
use Tighten\BaseLinter;
use Tighten\CustomNode;

class SpacesAroundBladeRenderContent extends BaseLinter
{
    protected $description = 'Spaces around blade rendered content:'
        . '`{{1 + 1}}` -> `{{ 1 + 1 }}`';

    public function lint(Parser $parser)
    {
        $foundNodes = [];

        /**
         * Normal render
         */
        foreach ($this->getCodeLines() as $line => $codeLine) {
            $matches = [];

            preg_match(
                '/\{\{(.+?)\}\}/',
                $codeLine,
                $matches
            );

            if (isset($matches[1]) && isset($matches[0])
                /** Is not a blade comment */
                && (substr($matches[1], 0, 2) !== '--')
                && (
                    /** Does not only have a *single* space before the start of the content */
                    $matches[1][0] !== ' '
                    || $matches[1][1] === ' '
                    /** Does not only have a *single* space at the end of the content */
                    || $matches[1][-1] !== ' '
                    || $matches[1][-2] === ' '
                )
            ) {
                $foundNodes[] = new CustomNode(['startLine' => $line + 1]);
            }
        }

        /**
         * Raw render
         */
        foreach ($this->getCodeLines() as $line => $codeLine) {
            $matches = [];

            preg_match(
                '/\{\!!s*(.+?)\s*!!\}/',
                $codeLine,
                $matches
            );

            if (isset($matches[1]) && (! Str::contains($codeLine, '{!! ') && ! Str::contains($codeLine, ' !!}'))) {
                $foundNodes[] = new CustomNode(['startLine' => $line + 1]);
            }
        }

        return $foundNodes;
    }
}
