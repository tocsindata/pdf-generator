<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\Layout;

use Famoser\PdfGenerator\Frontend\Layout\Traits\ElementTrait;
use Famoser\PdfGenerator\Frontend\LayoutEngine\ElementVisitorInterface;

abstract class AbstractElement
{
    use ElementTrait;

    /**
     * @template T
     *
     * @param ElementVisitorInterface<T> $visitor
     *
     * @return T
     */
    abstract public function accept(ElementVisitorInterface $visitor);
}
