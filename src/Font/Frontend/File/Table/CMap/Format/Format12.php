<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\Format;

use Famoser\PdfGenerator\Font\Frontend\File\Table\CMap\FormatVisitorInterface;

class Format12 extends Format
{
    /**
     * length.
     *
     * @ttf-type uint32
     */

    /**
     * language.
     *
     * @ttf-type uint32
     */

    /**
     * number of groupings.
     *
     * @ttf-type uint32
     *
     * @return int
     */
    private int $nGroups;

    /**
     * @var Format12Group[]
     */
    private array $groups = [];

    /**
     * the format of the encoding.
     *
     * @ttf-type fixed32
     */
    public function getFormat(): int
    {
        return self::FORMAT_12;
    }

    public function getNGroups(): int
    {
        return $this->nGroups;
    }

    public function setNGroups(int $nGroups): void
    {
        $this->nGroups = $nGroups;
    }

    /**
     * @return Format12Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    public function addGroup(Format12Group $group): void
    {
        $this->groups[] = $group;
    }

    public function accept(FormatVisitorInterface $formatVisitor)
    {
        return $formatVisitor->visitFormat12($this);
    }
}
