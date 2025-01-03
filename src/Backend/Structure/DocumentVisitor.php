<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\Backend\Structure;

use Famoser\PdfGenerator\Backend\Catalog\Font\Structure\CIDFont;
use Famoser\PdfGenerator\Backend\Catalog\Font\Structure\CIDSystemInfo;
use Famoser\PdfGenerator\Backend\Catalog\Font\Structure\FontDescriptor;
use Famoser\PdfGenerator\Backend\Catalog\Font\Structure\FontStream;
use Famoser\PdfGenerator\Backend\Catalog\Font\TrueType;
use Famoser\PdfGenerator\Backend\Catalog\Font\Type0;
use Famoser\PdfGenerator\Backend\Catalog\Font\Type1;
use Famoser\PdfGenerator\Backend\Catalog\Image as CatalogImage;
use Famoser\PdfGenerator\Backend\Catalog\Metadata;
use Famoser\PdfGenerator\Backend\CatalogVisitor;
use Famoser\PdfGenerator\Backend\Structure\Document\Font\CMapCreator;
use Famoser\PdfGenerator\Backend\Structure\Document\Font\DefaultFont;
use Famoser\PdfGenerator\Backend\Structure\Document\Font\EmbeddedFont;
use Famoser\PdfGenerator\Backend\Structure\Document\Image;
use Famoser\PdfGenerator\Backend\Structure\Optimization\Configuration;
use Famoser\PdfGenerator\Backend\Structure\Optimization\FontOptimizer;
use Famoser\PdfGenerator\Backend\Structure\Optimization\ImageOptimizer;
use Famoser\PdfGenerator\Backend\Structure\Xml\Node;
use Famoser\PdfGenerator\Backend\Structure\Xml\Terminal;
use Famoser\PdfGenerator\Font\Frontend\File\Table\HHeaTable;
use Famoser\PdfGenerator\Font\Frontend\File\Table\OS2Table;
use Famoser\PdfGenerator\Font\IR\Structure\Character;
use Famoser\PdfGenerator\Font\IR\Structure\Font;

class DocumentVisitor
{
    /**
     * @var int[]
     */
    private array $resourceCounters = [];

    private readonly FontOptimizer $fontOptimizer;

    private readonly CMapCreator $cMapCreator;

    private readonly ImageOptimizer $imageOptimizer;

    public function __construct(private readonly Configuration $configuration)
    {
        $this->fontOptimizer = new FontOptimizer();
        $this->cMapCreator = new CMapCreator($this->configuration->getMinimalCMapSize());
        $this->imageOptimizer = new ImageOptimizer();
    }

    private function generateIdentifier(string $prefix): string
    {
        if (!\array_key_exists($prefix, $this->resourceCounters)) {
            $this->resourceCounters[$prefix] = 0;

            return $prefix;
        }

        return $prefix . $this->resourceCounters[$prefix]++;
    }

    public function visitImage(Image $param): CatalogImage
    {
        $identifier = $this->generateIdentifier('I');
        $type = Image::TYPE_JPG === $param->getType() || Image::TYPE_JPEG === $param->getType() ? CatalogImage::IMAGE_TYPE_JPEG : null;

        $content = $param->getImageContent();
        $width = $param->getWidth();
        $height = $param->getHeight();

        // TODO: move to IR, not PDF specific
        if ($this->configuration->getAutoResizeImages()) {
            [$targetWidth, $targetHeight] = $this->imageOptimizer->getTargetHeightWidth($width, $height, $param->getMaxUsedWidth(), $param->getMaxUsedHeight(), $this->configuration->getAutoResizeImagesDpi());

            if ($targetWidth < $width) {
                $width = $targetWidth;
                $height = $targetHeight;

                $content = $this->imageOptimizer->transformToJpgAndResize($content, $width, $height);
                $type = CatalogImage::IMAGE_TYPE_JPEG;
            }
        }

        if (null === $type) {
            $content = $this->imageOptimizer->transformToJpgAndResize($content, $width, $height);
            $type = CatalogImage::IMAGE_TYPE_JPEG;
        }

        return new CatalogImage($identifier, $type, $content, $width, $height);
    }

    public function visitDefaultFont(DefaultFont $param): Type1
    {
        $identifier = $this->generateIdentifier('F');

        return new Type1($identifier, $param->getBaseFont());
    }

    /**
     * @throws \Exception
     */
    public function visitEmbeddedFont(EmbeddedFont $param): TrueType|Type0
    {
        $font = $param->getFont();
        $fontData = $param->getFontData();

        $createFontSubsets = $this->configuration->getCreateFontSubsets();
        if ($createFontSubsets) {
            [$font, $fontData, $usedCodepoints] = $this->fontOptimizer->createFontSubset($font, $param->getCharactersUsedInText());
        }

        $fontName = $font->getFontInformation()->getFullName() ?? 'invalidFontName';
        $fontName = strtr($fontName, [' ' => '']); // remove any spaces in name

        $fontStream = new FontStream(FontStream::SUBTYPE_OPEN_TYPE, $fontData);

        // glyph space -> text space (=em) is in units of 1000 for PDF
        // the font defines its own sizing in the head table, hence we need to normalize the units
        $sizeNormalizer = 1000 / $font->getTableDirectory()->getHeadTable()->getUnitsPerEm();
        $fontDescriptor = $this->getFontDescriptor($fontName, $font, $fontStream, $sizeNormalizer);

        if ($createFontSubsets && !$this->configuration->getUseTTFFonts()) {
            return $this->createType0Font($fontDescriptor, $font, $sizeNormalizer, $usedCodepoints);
        }

        return $this->createTrueTypeFont($fontDescriptor, $font->getCharacters(), $sizeNormalizer);
    }

    /**
     * @param Character[] $characters
     */
    private function createTrueTypeFont(FontDescriptor $fontDescriptor, array $characters, float $sizeNormalizer): TrueType
    {
        // default value is 0
        $widths = array_fill(0, 255, 0);

        // add widths of windows code page
        foreach ($characters as $character) {
            // create windows character set
            $mappingIndex = $character->getUnicodePoint() ? $this->getWindows1252Mapping($character->getUnicodePoint()) : null;
            if (null !== $mappingIndex) {
                $widths[$mappingIndex] = (int)($character->getLongHorMetric()->getAdvanceWidth() * $sizeNormalizer);
            }
        }

        $identifier = $this->generateIdentifier('F');

        return new TrueType($identifier, $fontDescriptor, $widths);
    }

    /**
     * @param int[] $usedCodepoints
     */
    private function createType0Font(FontDescriptor $fontDescriptor, Font $font, float $sizeNormalizer, array $usedCodepoints): Type0
    {
        /** @var int[] $characterWidths */
        $characterWidths = [];
        $characters = array_merge($font->getReservedCharacters(), $font->getCharacters());
        foreach ($characters as $character) {
            $characterWidths[] = (int)($character->getLongHorMetric()->getAdvanceWidth() * $sizeNormalizer);
        }

        // start at CID 0 with our widths
        $widths = [0 => $characterWidths];

        $cIDSystemInfo = new CIDSystemInfo('famoser', 'custom-1', 1);
        $cidFont = new CIDFont(CIDFont::SUBTYPE_CID_FONT_TYPE_2, $fontDescriptor->getFontName(), $cIDSystemInfo, $fontDescriptor, 500, $widths);

        $identifier = $this->generateIdentifier('F');

        $cMapName = $fontDescriptor->getFontName() . 'CMap';
        $characterIndexCMap = $this->cMapCreator->createTextToCharacterIndexCMap($cIDSystemInfo, $cMapName, $characters, $usedCodepoints);

        $cMapInvertedName = $fontDescriptor->getFontName() . 'CMapInverted';
        $unicodeCMap = $this->cMapCreator->createToUnicodeCMap($cIDSystemInfo, $cMapInvertedName, $characters);

        return new Type0($identifier, $fontDescriptor->getFontName(), $characterIndexCMap, $cidFont, $unicodeCMap);
    }

    private function getFontDescriptor(string $fontName, Font $font, FontStream $fontStream, float $sizeNormalizer): FontDescriptor
    {
        $HHeaTable = $font->getTableDirectory()->getHHeaTable();
        $OS2Table = $font->getTableDirectory()->getOS2Table();

        $angle = $this->getFontItalicAngle($HHeaTable);
        $fontFlags = $this->calculateFontFlags($OS2Table, $angle > 0);
        $BBox = $this->getFontBBox($font->getCharacters(), $sizeNormalizer);
        $ascent = (int)($HHeaTable->getAscent() * $sizeNormalizer);
        $descent = (int)($HHeaTable->getDescent() * $sizeNormalizer);
        $capHeight = (int)($OS2Table->getSCapHeight() * $sizeNormalizer);
        $stemVGuess = 0; // TODO find out where to get this from

        return new FontDescriptor($fontName, $fontFlags, $BBox, (int)$angle, $ascent, $descent, $capHeight, $stemVGuess, $fontStream);
    }

    /**
     * @param Character[] $characters
     *
     * @return int[]
     */
    private function getFontBBox(array $characters, float $sizeNormalizer): array
    {
        $xMin = 0;
        $xMax = 0;
        $yMin = 0;
        $yMax = 0;
        foreach ($characters as $character) {
            if (null === $character->getGlyfTable()) {
                continue;
            }

            $xMin = max($xMin, $character->getGlyfTable()->getXMin());
            $xMax = max($xMax, $character->getGlyfTable()->getXMax());
            $yMin = max($yMin, $character->getGlyfTable()->getYMin());
            $yMax = max($yMax, $character->getGlyfTable()->getYMax());
        }

        return [(int)($xMin * $sizeNormalizer), (int)($yMin * $sizeNormalizer), (int)($xMax * $sizeNormalizer), (int)($yMax * $sizeNormalizer)];
    }

    private function getFontItalicAngle(HHeaTable $HHeaTable): float
    {
        if (0 === $HHeaTable->getCaretSlopeRun()) {
            return 0;
        }

        return tanh($HHeaTable->getCaretSlopeRise() / $HHeaTable->getCaretSlopeRun()) - 90;
    }

    private function calculateFontFlags(OS2Table $OS2Table, bool $isItalic): int
    {
        $flags = 0;

        $panose = $OS2Table->getPanose();

        // fixed pitch
        if (9 === $panose[3]) { // when proportion is monospaced
            $flags = $flags | FontDescriptor::FLAG_FIXED_PITCH;
        }

        // serif
        if ($panose[1] >= 11 && $panose[1] <= 13) { // when serif style is normal sans, obtuse sans or perpendicular sans
            $flags = $flags | FontDescriptor::FLAG_SERIF;
        }

        // always symbolic (characters outside adobe standard set)
        $flags = $flags | FontDescriptor::FLAG_SYMBOLIC;

        // script (cursive)
        if (3 === $panose[0]) { // when family type is hand-written
            $flags = $flags | FontDescriptor::FLAG_SCRIPT;
        }

        // italic
        if ($isItalic) {
            $flags = $flags | FontDescriptor::FLAG_ITALIC;
        }

        return $flags;
    }

    private function getWindows1252Mapping(int $unicodePoint): ?int
    {
        if ($unicodePoint < 0x80) {
            return $unicodePoint;
        }

        if ($unicodePoint >= 0xA0 && $unicodePoint <= 0xFF) {
            return $unicodePoint;
        }

        return match ($unicodePoint) {
            0x20AC => 0x80,
            0x201A => 0x82,
            0x0192 => 0x83,
            0x201E => 0x84,
            0x2026 => 0x85,
            0x2020 => 0x86,
            0x2021 => 0x87,
            0x20C6 => 0x88,
            0x2030 => 0x89,
            0x0160 => 0x8A,
            0x2039 => 0x8B,
            0x0152 => 0x8C,
            0x017D => 0x8E,
            0x2018 => 0x91,
            0x2019 => 0x92,
            0x201C => 0x93,
            0x201D => 0x94,
            0x2022 => 0x95,
            0x2013 => 0x96,
            0x2014 => 0x97,
            0x02DC => 0x98,
            0x2122 => 0x99,
            0x0161 => 0x9A,
            0x203A => 0x9B,
            0x0153 => 0x9C,
            0x017E => 0x9E,
            0x0178 => 0x9F,
            default => null,
        };
    }

    public function visitXmpMeta(Document\XmpMeta $param): Metadata
    {
        $creatorAgentText = 'Famoser pdf-generator ' . CatalogVisitor::GENERATOR_VERSION;
        $content = [];

        // http://ns.adobe.com/xap/1.0/ for basic attributes (see https://developer.adobe.com/xmp/docs/XMPNamespaces/xmp/)
        $content[] = new Terminal('xmp:CreateDate', (new \DateTime())->format('c'));
        $content[] = new Terminal('xmp:CreatorTool', $creatorAgentText);

        // http://ns.adobe.com/pdf/1.3/ for PDF meta data (see https://developer.adobe.com/xmp/docs/XMPNamespaces/pdf/)
        $content[] = new Terminal('pdf:PDFVersion', '1.7');
        $content[] = new Terminal('pdf:Producer', $creatorAgentText);
        $pdf = $param->getPdf();
        if ($pdf->getKeywords()) {
            $content[] = new Terminal('pdf:Keywords', $pdf->getKeywords());
        }

        // http://purl.org/dc/elements/1.1/ for content meta data (see https://developer.adobe.com/xmp/docs/XMPNamespaces/dc/)
        $core = $param->getCoreElements();
        $content[] = new Terminal('dc:format', 'application/pdf');
        $content[] = $this->createNodeIfNotEmpty('language', false, $core->getLanguage());
        $content[] = $this->createLanguageNodeIfNotEmpty('title', $core->getTitle());
        $content[] = $this->createLanguageNodeIfNotEmpty('description', $core->getDescription());

        $content[] = $this->createNodeIfNotEmpty('creator', true, $core->getCreators());
        $content[] = $this->createNodeIfNotEmpty('contributor', false, $core->getContributors());

        $content[] = $this->createNodeIfNotEmpty('publisher', false, $core->getPublisher());
        $content[] = $this->createNodeIfNotEmpty('subject', false, $core->getSubject());
        $content[] = $this->createNodeIfNotEmpty('date', true, $core->getDate());

        // http://ns.adobe.com/xap/1.0/mm/ for versioning (see https://developer.adobe.com/xmp/docs/XMPNamespaces/xmpMM/)
        $documentId = uniqid();
        $content[] = new Terminal('xmpMM:DocumentID', $documentId);
        $content[] = new Terminal('xmpMM:InstanceID', $documentId);

        $rdfDescription = new Node('rdf:Description', array_filter($content), ['xmlns:xmp' => 'http://ns.adobe.com/xap/1.0/', 'xmlns:pdf' => 'http://ns.adobe.com/pdf/1.3/', 'xmlns:dc' => 'http://purl.org/dc/elements/1.1/', 'xmlns:xmpMM' => 'http://ns.adobe.com/xap/1.0/mm/']);
        $rdf = new Node('rdf:RDF', [$rdfDescription], ['xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#']);
        $node = new Node('x:xmpmeta', [$rdf], ['xmlns:x' => 'adobe:ns:meta/', 'x:xmptk' => $creatorAgentText]);

        $serializer = new XmlSerializerVisitor();
        $xml = $node->visit($serializer);

        $mainLanguage = count($core->getLanguage()) > 0 ? $core->getLanguage()[0] : null;
        $mainTitle = $core->getTitle()[$mainLanguage] ?? null;
        return new Metadata($xml, $mainLanguage, $mainTitle, implode(", ", $core->getCreators()), $pdf->getKeywords());
    }

    /**
     * @param string[] $terminalValues
     */
    public function createNodeIfNotEmpty(string $dcTag, bool $ordered, array $terminalValues): ?Node
    {
        if (count($terminalValues) === 0) {
            return null;
        }

        $terminals = [];
        foreach ($terminalValues as $terminalValue) {
            $terminals[] = new Terminal('rdf:li', $terminalValue);
        }

        return $this->wrapDcRdfContentNode($dcTag, $ordered ? 'Seq' : 'Bag', $terminals);
    }

    /**
     * @param array<string, string> $languageValues
     */
    public function createLanguageNodeIfNotEmpty(string $dcTag, array $languageValues): ?Node
    {
        if (count($languageValues) === 0) {
            return null;
        }

        $terminals = [];
        foreach ($languageValues as $language => $languageValue) {
            $terminals[] = new Terminal('rdf:li', $languageValue, ['xml:lang' => $language]);
        }

        return $this->wrapDcRdfContentNode($dcTag, 'Alt', $terminals);
    }

    /**
     * @param Terminal[] $terminals
     */
    public function wrapDcRdfContentNode(string $dcTag, string $rdfArray, array $terminals): Node
    {
        $array = new Node('rdf:' . $rdfArray, $terminals);
        return new Node('dc:' . $dcTag, [$array]);
    }
}
