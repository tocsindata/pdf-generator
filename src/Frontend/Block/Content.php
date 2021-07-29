<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Frontend\Block;

use PdfGenerator\Frontend\Block\Base\Block;
use PdfGenerator\Frontend\Block\Style\Base\BlockStyle;
use PdfGenerator\Frontend\Block\Style\ContentStyle;
use PdfGenerator\Frontend\BlockVisitor;
use PdfGenerator\Frontend\MeasuredContent\Base\MeasuredContent;

class Content extends Block
{
    /**
     * @var ContentStyle
     */
    private $style;

    /**
     * @var MeasuredContent
     */
    private $measuredContent;

    /**
     * Content constructor.
     */
    public function __construct(MeasuredContent $measuredContent, ContentStyle $style = null, array $dimensions = null)
    {
        parent::__construct($dimensions);

        $this->measuredContent = $measuredContent;
        $this->style = $style ?? new BlockStyle();
    }

    public function getStyle(): ContentStyle
    {
        return $this->style;
    }

    public function getMeasuredContent(): MeasuredContent
    {
        return $this->measuredContent;
    }

    public function accept(BlockVisitor $blockVisitor)
    {
        // TODO: Implement accept() method.
    }
}