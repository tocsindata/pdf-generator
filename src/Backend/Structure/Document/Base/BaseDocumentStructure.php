<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Base;

use Famoser\PdfGenerator\Backend\Structure\DocumentVisitor;

abstract readonly class BaseDocumentStructure
{
    abstract public function accept(DocumentVisitor $documentVisitor): mixed;
}
