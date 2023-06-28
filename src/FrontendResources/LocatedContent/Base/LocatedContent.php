<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\FrontendResources\LocatedContent\Base;

use PdfGenerator\FrontendResources\Position;
use PdfGenerator\FrontendResources\Size;

class LocatedContent
{
    public function __construct(private readonly Position $position, private readonly Size $size)
    {
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getSize(): Size
    {
        return $this->size;
    }
}