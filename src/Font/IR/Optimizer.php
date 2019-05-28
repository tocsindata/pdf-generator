<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Font\IR;

use PdfGenerator\Font\IR\Structure\Character;
use PdfGenerator\Font\IR\Structure\Font;
use PdfGenerator\Font\IR\Structure\TableDirectory;

class Optimizer
{
    /**
     * @return Optimizer
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param Font $source
     * @param Character[] $characters
     *
     * @return Font
     */
    public function getFontSubset(Font $source, array $characters)
    {
        $font = new Font();

        $font->setMissingGlyphCharacter($source->getMissingGlyphCharacter());
        $font->setCharacters($characters);
        $font->setTableDirectory($this->getTableDirectoryAfterSubsetting($source->getTableDirectory()));

        return $font;
    }

    /**
     * @param TableDirectory $source
     *
     * @return TableDirectory
     */
    private function getTableDirectoryAfterSubsetting(TableDirectory $source)
    {
        $rawTableDirectory = new TableDirectory();

        $rawTableDirectory->setCvtTable($source->getCvtTable());
        $rawTableDirectory->setFpgmTable($source->getFpgmTable());

        /*
         * intentionally skipping GDEF, GPOST, GSUB as these are dependant on glyphs
         */

        $rawTableDirectory->setGaspTable($source->getGaspTable());
        $rawTableDirectory->setHeadTable($source->getHeadTable());
        $rawTableDirectory->setHHeaTable($source->getHHeaTable());
        $rawTableDirectory->setMaxPTable($source->getMaxPTable());
        $rawTableDirectory->setNameTable($source->getNameTable());
        $rawTableDirectory->setOS2Table($source->getOS2Table());
        $rawTableDirectory->setPostTable($source->getPostTable());
        $rawTableDirectory->setPrepTable($source->getPrepTable());

        // per default include unknown tables
        $rawTableDirectory->setRawTables($source->getRawTables());

        return $rawTableDirectory;
    }
}