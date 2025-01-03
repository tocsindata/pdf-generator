<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\File\Token\Base;

use Famoser\PdfGenerator\Backend\File\TokenVisitor;

abstract class BaseToken
{
    abstract public function accept(TokenVisitor $visitor): string;
}
