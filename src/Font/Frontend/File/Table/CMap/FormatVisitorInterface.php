<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\CMap;

use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format0;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format12;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format4;
use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format\Format6;

/**
 * @template T
 */
interface FormatVisitorInterface
{
    /**
     * @return T
     */
    public function visitFormat0(Format0 $format0);

    /**
     * @return T
     */
    public function visitFormat4(Format4 $format4);

    /**
     * @return T
     */
    public function visitFormat6(Format6 $format6);

    /**
     * @return T
     */
    public function visitFormat12(Format12 $format12);
}
