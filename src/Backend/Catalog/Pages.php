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

use Famoser\PdfGenerator\Backend\Catalog\Base\BaseStructure;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\File\Object\Base\BaseObject;

readonly class Pages extends BaseStructure
{
    /**
     * @param Page[] $pages
     */
    public function __construct(private array $pages)
    {
    }

    public function accept(CatalogVisitor $visitor): BaseObject
    {
        return $visitor->visitPages($this);
    }

    /**
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }
}
