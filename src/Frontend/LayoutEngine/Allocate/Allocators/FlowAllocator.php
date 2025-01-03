<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\Allocators;

use Famoser\PdfGenerator\Frontend\Layout\AbstractBlock;
use Famoser\PdfGenerator\Frontend\Layout\Flow;
use Famoser\PdfGenerator\Frontend\Layout\Style\FlowDirection;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocation;
use Famoser\PdfGenerator\Frontend\LayoutEngine\Allocate\BlockAllocationVisitor;

readonly class FlowAllocator
{
    public function __construct(private float $width, private float $height)
    {
    }

    /**
     * @param AbstractBlock[] $overflowBlocks
     *
     * @return BlockAllocation[]
     */
    public function allocate(Flow $flow, array &$overflowBlocks = [], float &$usedWidth = 0, float &$usedHeight = 0): array
    {
        /** @var BlockAllocation[] $blockAllocations */
        $blockAllocations = [];
        $overflowBlocks = $flow->getBlocks();
        while (count($overflowBlocks) > 0) {
            $block = array_shift($overflowBlocks);

            // get allocation of child
            $availableWidth = FlowDirection::ROW === $flow->getDirection() ? $this->width - $usedWidth : $this->width;
            $availableHeight = FlowDirection::COLUMN === $flow->getDirection() ? $this->height - $usedHeight : $this->height;
            $allocationVisitor = new BlockAllocationVisitor($availableWidth, $availableHeight);
            /** @var BlockAllocation $allocation */
            $allocation = $block->accept($allocationVisitor);

            $progressMade = count($blockAllocations) > 0;
            $overflow = $allocation->getWidth() > $availableWidth || $allocation->getHeight() > $availableHeight;
            if ($progressMade && $overflow) {
                array_unshift($overflowBlocks, $block);
                break;
            }

            // update allocated content
            if (FlowDirection::ROW === $flow->getDirection()) {
                $blockAllocations[] = new BlockAllocation($usedWidth, 0, $allocation->getWidth(), $allocation->getHeight(), [$allocation]);
                $usedHeight = max($usedHeight, $allocation->getHeight());
                $usedWidth += $allocation->getWidth() + $flow->getGap();
            } else {
                $blockAllocations[] = new BlockAllocation(0, $usedHeight, $allocation->getWidth(), $allocation->getHeight(), [$allocation]);
                $usedHeight += $allocation->getHeight() + $flow->getGap();
                $usedWidth = max($usedWidth, $allocation->getWidth());
            }

            if ($allocation->getOverflow()) {
                array_unshift($overflowBlocks, $allocation->getOverflow());
            }
        }

        if (count($blockAllocations) > 0) {
            // remove gap from last iteration
            if (FlowDirection::ROW === $flow->getDirection()) {
                $usedWidth -= $flow->getGap();
            } else {
                $usedHeight -= $flow->getGap();
            }
        }

        return $blockAllocations;
    }
}
