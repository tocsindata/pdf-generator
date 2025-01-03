<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\IR\Structure;

use Famoser\PdfGenerator\Font\Frontend\File\Table\GlyfTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HMtx\LongHorMetric;

class Character
{
    private ?int $unicodePoint = null;

    private PostScriptInfo $postScriptInfo;

    private ?BoundingBox $boundingBox = null;

    private ?GlyfTable $glyfTable = null;

    private LongHorMetric $longHorMetric;

    /**
     * carefull: entries in array could be null for invalid font files.
     *
     * @var Character[]
     */
    private array $componentCharacters = [];

    public function getUnicodePoint(): ?int
    {
        return $this->unicodePoint;
    }

    public function setUnicodePoint(int $unicodePoint): void
    {
        $this->unicodePoint = $unicodePoint;
    }

    public function getPostScriptInfo(): ?PostScriptInfo
    {
        return $this->postScriptInfo;
    }

    public function setPostScriptInfo(PostScriptInfo $postScriptInfo): void
    {
        $this->postScriptInfo = $postScriptInfo;
    }

    public function getBoundingBox(): ?BoundingBox
    {
        return $this->boundingBox;
    }

    public function setBoundingBox(?BoundingBox $boundingBox): void
    {
        $this->boundingBox = $boundingBox;
    }

    public function getGlyfTable(): ?GlyfTable
    {
        return $this->glyfTable;
    }

    public function setGlyfTable(?GlyfTable $glyfTable): void
    {
        $this->glyfTable = $glyfTable;
    }

    public function getLongHorMetric(): LongHorMetric
    {
        return $this->longHorMetric;
    }

    public function setLongHorMetric(LongHorMetric $longHorMetric): void
    {
        $this->longHorMetric = $longHorMetric;
    }

    /**
     * @return Character[]
     */
    public function getComponentCharacters(): array
    {
        return $this->componentCharacters;
    }

    public function addComponentCharacter(?self $character): void
    {
        $this->componentCharacters[] = $character;
    }
}
