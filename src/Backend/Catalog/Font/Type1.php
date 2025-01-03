<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog\Font;

use Famoser\PdfGenerator\Backend\Catalog\Font;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;
use Famoser\PdfGenerator\Backend\File\Object\DictionaryObject;

readonly class Type1 extends Font
{
    public const BASE_FONT_TIMES__ROMAN = 'Times-Roman';
    public const BASE_FONT_HELVETICA = 'Helvetica';
    public const BASE_FONT_COURIER = 'Courier';
    public const BASE_FONT_SYMBOL = 'Symbol';
    public const BASE_FONT_TIMES__BOLD = 'Times-Bold';
    public const BASE_FONT_HELVETICA__BOLD = 'Helvetica-Bold';
    public const BASE_FONT_COURIER__BOLD = 'Courier-Bold';
    public const BASE_FONT_ZAPFDINGBATS = 'ZapfDingbats';
    public const BASE_FONT_TIMES__ITALIC = 'Times-Italic';
    public const BASE_FONT_HELVETICA__OBLIQUE = 'Helvetica-Oblique';
    public const BASE_FONT_COURIER__OBLIQUE = 'Courier-Oblique';
    public const BASE_FONT_TIMES__BOLDITALIC = 'Times-BoldItalic';
    public const BASE_FONT_HELVETICA__BOLDOBLIQUE = 'Helvetica-BoldOblique';
    public const BASE_FONT_COURIER__BOLDOBLIQUE = 'Courier-BoldOblique';

    public const ENCODING_WIN_ANSI_ENCODING = 'WinAnsiEncoding';

    public function __construct(string $identifier, private string $baseFont)
    {
        parent::__construct($identifier);
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function getEncoding(): string
    {
        return self::ENCODING_WIN_ANSI_ENCODING;
    }

    public function accept(CatalogVisitor $visitor): DictionaryObject
    {
        return $visitor->visitType1Font($this);
    }

    /**
     * sets the encoding used by the font.
     */
    public function encode(string $value): string
    {
        /* windows-1252 is equivalent WinAnsiEncoding according to comment https://www.php.net/manual/de/haru.builtin.encodings.php. */
        return mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
    }
}
