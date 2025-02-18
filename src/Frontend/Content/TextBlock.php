<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Content;

use Famoser\PdfGenerator\Frontend\Content\Text\TextLine;
use Famoser\PdfGenerator\Frontend\Layout\Text\Structure;

readonly class TextBlock extends AbstractContent
{
    /**
     * @param TextLine[] $lines
     */
    public function __construct(private float $width, private float $height, private array $lines, private Structure $level = Structure::Paragraph)
    {
        parent::__construct($this->width, $this->height);
    }

    /**
     * @return TextLine[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getLevel(): Structure
    {
        return $this->level;
    }

    public function accept(ContentVisitorInterface $contentVisitor): void
    {
        $contentVisitor->visitTextBlock($this);
    }
}
