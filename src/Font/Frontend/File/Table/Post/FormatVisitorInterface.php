<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\Post;

use Famoser\PdfGenerator\Font\Frontend\File\Table\Post\Format\Format1;
use Famoser\PdfGenerator\Font\Frontend\File\Table\Post\Format\Format2;
use Famoser\PdfGenerator\Font\Frontend\File\Table\Post\Format\Format25;
use Famoser\PdfGenerator\Font\Frontend\File\Table\Post\Format\Format3;

/**
 * @template T
 */
interface FormatVisitorInterface
{
    /**
     * @return T
     */
    public function visitFormat1(Format1 $format1);

    /**
     * @return T
     */
    public function visitFormat2(Format2 $format2);

    /**
     * @return T
     */
    public function visitFormat25(Format25 $format25);

    /**
     * @return T
     */
    public function visitFormat3(Format3 $format3);
}
