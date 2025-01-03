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
use Famoser\PdfGenerator\Backend\Catalog\Font\Structure\CMap;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\DictionaryObject;

readonly class Type0 extends Font
{
    public function __construct(string $identifier, private string $baseFont, private CMap $encoding, private Structure\CIDFont $descendantFont, private CMap $toUnicode)
    {
        parent::__construct($identifier);
    }

    public function getBaseFont(): string
    {
        return $this->baseFont;
    }

    public function getEncoding(): CMap
    {
        return $this->encoding;
    }

    public function getDescendantFont(): Structure\CIDFont
    {
        return $this->descendantFont;
    }

    public function getToUnicode(): CMap
    {
        return $this->toUnicode;
    }

    public function accept(CatalogVisitor $visitor): DictionaryObject
    {
        return $visitor->visitType0Font($this);
    }

    public function encode(string $value): string
    {
        return mb_convert_encoding($value, 'UTF-8');
    }
}
