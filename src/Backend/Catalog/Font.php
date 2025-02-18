<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Catalog;

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseIdentifiableStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

abstract readonly class Font extends BaseIdentifiableStructure
{
    public function __construct(string $identifier)
    {
        parent::__construct($identifier);
    }

    abstract public function encode(string $value): string;

    abstract public function accept(CatalogVisitor $visitor): BaseObject;
}
