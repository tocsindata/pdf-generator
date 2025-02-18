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

use Famoser\PdfGenerator\Frontend\Content\Style\TextStyle;
use Famoser\PdfGenerator\Frontend\Content\Text\TextLine;
use Famoser\PdfGenerator\Frontend\Content\Text\TextSegment;
use Famoser\PdfGenerator\Frontend\Content\TextBlock;
use Famoser\PdfGenerator\Frontend\Layout\Text;
use Famoser\PdfGenerator\Frontend\Layout\Text\Alignment;
use Famoser\PdfGenerator\Frontend\Layout\TextSpan;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontMeasurement;
use Famoser\PdfGenerator\Frontend\Resource\Font\FontRepository;

readonly class TextAllocator
{
    private FontRepository $fontRepository;

    public function __construct(private float $width = PHP_FLOAT_MAX, private float $height = PHP_FLOAT_MAX)
    {
        $this->fontRepository = FontRepository::instance();
    }

    /**
     * @param TextSpan[] $overflowSpans
     */
    public function allocate(Text $text, array &$overflowSpans = [], float &$usedWidth = 0, float &$usedHeight = 0): TextBlock
    {
        /** @var TextLine[] $allocatedLines */
        $allocatedLines = [];
        $usedHeight = 0.0;
        $usedWidth = 0.0;
        $overflowSpans = $text->getSpans();
        while (count($overflowSpans) > 0) {
            $allocatedLineWidth = 0.0;
            $remainingSpans = [];
            $line = $this->allocateLine($text->getAlignment(), $this->width, $overflowSpans, $allocatedLineWidth, $remainingSpans);

            // cannot allocate, too high
            if ($usedHeight + $line->getLeading() > $this->height && count($allocatedLines) > 0) {
                break;
            }

            $allocatedLines[] = $line;
            $usedHeight += $line->getLeading();
            $usedWidth = max($usedWidth, $allocatedLineWidth);
            $overflowSpans = $remainingSpans;
        }

        // correct vertical height calculation (boundary lines do not need full line gap)
        if (count($allocatedLines) > 0) {
            $firstLine = $allocatedLines[0];
            $usedHeight -= $firstLine->getBoundaryCorrection();

            $lastLine = $allocatedLines[count($allocatedLines) - 1];
            $usedHeight -= $lastLine->getBoundaryCorrection();
        }

        return new TextBlock($usedWidth, $usedHeight, $allocatedLines);
    }

    /**
     * @param TextSpan[] $spans
     * @param TextSpan[] $overflow
     */
    private function allocateLine(Alignment $alignment, float $maxWidth, array $spans, float &$allocatedWidth, array &$overflow = []): TextLine
    {
        $overflow = $spans;
        $allocatedSegments = [];
        $leading = 0.0;
        $baselineStart = 0.0;
        $boundaryCorrection = 0.0;
        $abortedByNewline = false;
        while ($span = array_shift($overflow)) {
            // allocate next segment
            $fontMeasurement = $this->fontRepository->getFontMeasurement($span);
            $availableWidth = $maxWidth - $allocatedWidth;
            $line = self::getLine($span->getText(), $nextLines);
            $allocatedLineWidth = 0.0;
            $overflowLine = '';
            $segment = $this->allocateSegment($span->getTextStyle(), $fontMeasurement, $availableWidth, $line, $allocatedLineWidth, $overflowLine);

            // cannot allocate, too wide
            if ($allocatedWidth + $allocatedLineWidth > $maxWidth && count($allocatedSegments) > 0) {
                array_unshift($overflow, $span);
                break;
            }

            $allocatedSegments[] = $segment;
            $allocatedWidth += $allocatedLineWidth;

            // chose max leading for each line
            if ($fontMeasurement->getLeading() > $leading) {
                $leading = $fontMeasurement->getLeading();
                $boundaryCorrection = $fontMeasurement->getLineGap() / 2;
                $baselineStart = $fontMeasurement->getAscender() + $boundaryCorrection;
            }

            // start next span if no overflow on line & no newline
            if (null === $nextLines && '' === $overflowLine) {
                continue;
            }

            // set overflow
            $remainingText = $overflowLine;

            // remove first space to logically replace space with (omitted) newline
            if (str_starts_with($remainingText, ' ')) {
                $remainingText = substr($remainingText, 1);
            }

            // re-add rest of lines
            if (null !== $nextLines) {
                // re-add newline if line not fully consumed & text after
                if ('' !== $remainingText) {
                    $remainingText .= "\n";
                }

                $remainingText .= $nextLines;
            }

            $span = $span->cloneWithText($remainingText);
            array_unshift($overflow, $span);

            // abort
            $abortedByNewline = null !== $nextLines && '' === $overflowLine;
            break;
        }

        // remove last space to logically replace space with (omitted) newline
        if (count($overflow) > 0 && count($allocatedSegments) > 0) {
            $lastSegmentIndex = count($allocatedSegments) - 1;
            $lastSegment = $allocatedSegments[$lastSegmentIndex];
            if (str_ends_with($lastSegment->getText(), ' ')) {
                $textWithoutSpace = substr($lastSegment->getText(), 0, -1);
                $allocatedSegments[$lastSegmentIndex] = $lastSegment->cloneWithText($textWithoutSpace);
                $allocatedWidth -= $lastSegment->getFontMeasurement()->getSpaceWidth();
            }
        }

        // handle alignment
        $offset = 0.0;
        $wordSpacing = 0.0;
        $remainingWidth = $maxWidth - $allocatedWidth;
        $allocatedWidth = Alignment::ALIGNMENT_LEFT === $alignment ? $allocatedWidth : $maxWidth;
        if (Alignment::ALIGNMENT_CENTER === $alignment) {
            $offset = $remainingWidth / 2;
        } elseif (Alignment::ALIGNMENT_RIGHT === $alignment) {
            $offset = $remainingWidth;
        } elseif (
            Alignment::ALIGNMENT_JUSTIFIED === $alignment
            && !$abortedByNewline // for newlines, do not justify
            && count($overflow) > 0 // for last line in paragraph, do not justify
        ) {
            $totalSpaceWidth = 0.0;
            foreach ($allocatedSegments as $allocatedSegment) {
                $spacesCount = mb_substr_count($allocatedSegment->getText(), ' ');
                $spaceWidth = $allocatedSegment->getFontMeasurement()->getSpaceWidth();
                $totalSpaceWidth += $spaceWidth * $spacesCount;
            }

            if ($totalSpaceWidth > 0) {
                // -1 as 0 is normal space, -1 is no space, 1 is 2x space, 2 is 3x space, etc
                $wordSpacing = (($totalSpaceWidth + $remainingWidth) / $totalSpaceWidth) - 1;
            }
        }

        return new TextLine($allocatedSegments, $leading, $baselineStart, $boundaryCorrection, $offset, $wordSpacing);
    }

    private function allocateSegment(TextStyle $textStyle, FontMeasurement $fontMeasurement, float $maxWidth, string $content, float &$allocatedWidth, string &$overflow): TextSegment
    {
        $overflow = $content;
        $allocatedText = '';
        while ('' !== $overflow) {
            $nextChunks = '';
            $chunk = self::getChunk($overflow, $nextChunks);
            $chunkWidth = $fontMeasurement->getWidth($chunk);

            if ($allocatedWidth + $chunkWidth > $maxWidth && '' !== $allocatedText) {
                break;
            }

            $allocatedText .= $chunk;
            $allocatedWidth += $chunkWidth;

            $overflow = $nextChunks;
        }

        return new TextSegment($allocatedText, $textStyle, $fontMeasurement);
    }

    public static function getLine(string $value, ?string &$nextLines = null): string
    {
        $cleanedText = str_replace("\r", '', $value); // ignore carriage return for now
        $singleLineEnd = mb_strpos($cleanedText, "\n");
        if (false === $singleLineEnd) {
            return $cleanedText;
        }

        $nextLines = mb_substr($cleanedText, $singleLineEnd + 1);

        return mb_substr($cleanedText, 0, $singleLineEnd);
    }

    public static function getChunk(string $value, string &$nextChunks = ''): string
    {
        $noPrefixValue = mb_ltrim($value);
        $chunkContentStart = mb_strlen($value) - mb_strlen($noPrefixValue);

        $chunkContentEnd = mb_strpos($value, ' ', $chunkContentStart);
        if (false === $chunkContentEnd) {
            return $value;
        }

        $nextChunks = mb_substr($value, $chunkContentEnd);

        return mb_substr($value, 0, $chunkContentEnd);
    }
}
