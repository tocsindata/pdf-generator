<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Catalog;

use PdfGenerator\Backend\Catalog\Base\BaseStructure;
use PdfGenerator\Backend\CatalogVisitor;
use PdfGenerator\Backend\File\Object\Base\BaseObject;
use PdfGenerator\Utils\IdentifiableTrait;

class Page extends BaseStructure
{
    use IdentifiableTrait;

    /**
     * @var Pages
     */
    private $parent;

    /**
     * @var int[]
     */
    private $mediaBox;

    /**
     * @var Contents
     */
    private $contents;

    /**
     * @var Resources
     */
    private $resources;

    /**
     * Page constructor.
     *
     * @param Pages $parent
     * @param array $mediaBox
     * @param Resources $resources
     * @param Contents $contents
     */
    public function __construct(Pages $parent, array $mediaBox, Resources $resources, Contents $contents)
    {
        $this->parent = $parent;
        $this->mediaBox = $mediaBox;
        $this->contents = $contents;
        $this->resources = $resources;
    }

    /**
     * @param CatalogVisitor $visitor
     *
     * @return BaseObject
     */
    public function accept(CatalogVisitor $visitor)
    {
        return $visitor->visitPage($this);
    }

    /**
     * @return Pages
     */
    public function getParent(): Pages
    {
        return $this->parent;
    }

    /**
     * @return int[]
     */
    public function getMediaBox(): array
    {
        return $this->mediaBox;
    }

    /**
     * @return Contents
     */
    public function getContents(): Contents
    {
        return $this->contents;
    }

    /**
     * @return Resources
     */
    public function getResources(): Resources
    {
        return $this->resources;
    }
}