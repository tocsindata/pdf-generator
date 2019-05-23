<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR\Structure;

class PostScriptInfo
{
    /**
     * @var int|null
     */
    private $macintoshGlyphIndex;

    /**
     * @var string
     */
    private $name;

    /**
     * @return bool
     */
    public function isInStandardMacintoshSet(): bool
    {
        return $this->macintoshGlyphIndex !== null;
    }

    /**
     * @return int|null
     */
    public function getMacintoshGlyphIndex(): ?int
    {
        return $this->macintoshGlyphIndex;
    }

    /**
     * @param int|null $macintoshGlyphIndex
     */
    public function setMacintoshGlyphIndex(?int $macintoshGlyphIndex): void
    {
        $this->macintoshGlyphIndex = $macintoshGlyphIndex;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}