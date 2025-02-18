<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Resource\Font;

use Famoser\PdfGenerator\Backend\Structure\Document\Font\DefaultFont as BackendDefaultFont;
use Famoser\PdfGenerator\IR\Document\Resource\Font;
use Famoser\PdfGenerator\IR\Document\Resource\Font\Utils\DefaultFontSizeLookup;
use Famoser\PdfGenerator\IR\DocumentVisitor;

readonly class DefaultFont extends Font
{
    final public const FONT_HELVETICA = 'Helvetica';
    final public const FONT_COURIER = 'Courier';
    final public const FONT_TIMES = 'Times';
    final public const FONT_SYMBOL = 'Symbol';
    final public const FONT_ZAPFDINGBATS = 'ZapfDingbats';

    final public const STYLE_DEFAULT = self::STYLE_ROMAN;
    final public const STYLE_ROMAN = 'ROMAN';
    final public const STYLE_ITALIC = 'ITALIC';
    final public const STYLE_BOLD = 'BOLD';
    final public const STYLE_OBLIQUE = 'OBLIQUE';
    final public const STYLE_BOLD_OBLIQUE = 'BOLD_OBLIQUE';
    final public const STYLE_BOLD_ITALIC = 'BOLD_ITALIC';

    /**
     * @param array<string,int> $size
     */
    private function __construct(private string $font, private string $style, private array $size)
    {
    }

    public static function create(string $font, string $style): self
    {
        $size = DefaultFontSizeLookup::getSize($font, $style);

        return new self($font, $style, $size);
    }

    public function accept(DocumentVisitor $visitor): BackendDefaultFont
    {
        return $visitor->visitDefaultFont($this);
    }

    public function acceptFont(FontVisitor $visitor)
    {
        return $visitor->visitDefaultFont($this);
    }

    public function getIdentifier(): string
    {
        return $this->font.'_'.$this->style;
    }

    public function getFont(): string
    {
        return $this->font;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function getUnitsPerEm(): int
    {
        return $this->size['unitsPerEm'];
    }

    public function getAscender(): int
    {
        return $this->size['ascender'];
    }

    public function getDescender(): int
    {
        return $this->size['descender'];
    }

    public function getLineGap(): int
    {
        return $this->size['lineGap'];
    }
}
