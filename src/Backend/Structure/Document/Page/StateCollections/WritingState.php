<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections;

use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\Base\BaseState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\ColorState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\GeneralGraphicState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\State\TextState;
use Famoser\PdfGenerator\Backend\Structure\Document\Page\StateCollections\Base\BaseStateCollection;

readonly class WritingState extends BaseStateCollection
{
    public function __construct(private GeneralGraphicState $generalGraphicsState, private ColorState $colorState, private TextState $textState)
    {
    }

    /**
     * @return BaseState[]
     */
    public function getState(): array
    {
        return [$this->generalGraphicsState, $this->colorState, $this->textState];
    }

    public function getGeneralGraphicsState(): GeneralGraphicState
    {
        return $this->generalGraphicsState;
    }

    public function getTextState(): TextState
    {
        return $this->textState;
    }
}
