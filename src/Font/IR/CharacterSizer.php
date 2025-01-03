<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\IR;

use Famoser\PdfGenerator\Font\IR\Structure\Font;

class CharacterSizer
{
    /**
     * @var int[]
     */
    private array $characterAdvanceWidthLookup = [];

    private readonly int $invalidCharacterWidth;

    public function __construct(Font $font)
    {
        $characters = array_merge($font->getReservedCharacters(), $font->getCharacters());
        foreach ($characters as $character) {
            $this->characterAdvanceWidthLookup[$character->getUnicodePoint()] = $character->getLongHorMetric()->getAdvanceWidth();
        }

        $this->invalidCharacterWidth = $font->getReservedCharacters()[0]->getLongHorMetric()->getAdvanceWidth();
    }

    public function isMonospace(): bool
    {
        return 1 === \count(array_unique($this->characterAdvanceWidthLookup));
    }

    /**
     * @return int[]
     */
    public function getCharacterAdvanceWidthLookup(): array
    {
        return $this->characterAdvanceWidthLookup;
    }

    public function getInvalidCharacterWidth(): int
    {
        return $this->invalidCharacterWidth;
    }
}
