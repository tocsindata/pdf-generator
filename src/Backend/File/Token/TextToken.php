<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\File\Token;

use Famoser\PdfGenerator\Backend\File\Token\Base\BaseToken;
use Famoser\PdfGenerator\Backend\File\TokenVisitor;

class TextToken extends BaseToken
{
    public function __construct(private readonly string $text)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function accept(TokenVisitor $visitor): string
    {
        return $visitor->visitTextToken($this);
    }
}
