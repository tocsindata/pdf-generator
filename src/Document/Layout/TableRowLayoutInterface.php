<?php

/*
 * This file is part of the mangel.io project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Document\Layout;

use PdfGenerator\Document\Layout\Base\ColumnedLayoutInterface;
use PdfGenerator\Document\Layout\Base\PrintableLayoutInterface;

interface TableRowLayoutInterface extends ColumnedLayoutInterface, PrintableLayoutInterface
{
}