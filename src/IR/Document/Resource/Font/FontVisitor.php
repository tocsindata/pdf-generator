<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Resource\Font;

/**
 * @template T
 */
interface FontVisitor
{
    /**
     * @return T
     */
    public function visitDefaultFont(DefaultFont $param);

    /**
     * @return T
     */
    public function visitEmbeddedFont(EmbeddedFont $param);
}
