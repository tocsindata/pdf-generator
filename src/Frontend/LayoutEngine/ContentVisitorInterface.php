<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine;

use Famoser\PdfGenerator\Frontend\Content\ImagePlacement;
use Famoser\PdfGenerator\Frontend\Content\Paragraph;
use Famoser\PdfGenerator\Frontend\Content\Rectangle;
use Famoser\PdfGenerator\Frontend\Content\Spacer;

/**
 * @template T
 */
interface ContentVisitorInterface
{
    /**
     * @return T
     */
    public function visitParagraph(Paragraph $paragraph);

    /**
     * @return T
     */
    public function visitRectangle(Rectangle $rectangle);

    /**
     * @return T
     */
    public function visitSpacer(Spacer $spacer);

    /**
     * @return T
     */
    public function visitImagePlacement(ImagePlacement $imagePlacement);
}
