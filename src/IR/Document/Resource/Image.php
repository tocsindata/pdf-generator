<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Famoser\PdfGenerator\IR\Document\Resource;

use Famoser\PdfGenerator\IR\Document\Base\BaseDocumentResource;
use Famoser\PdfGenerator\IR\DocumentVisitor;

readonly class Image extends BaseDocumentResource
{
    final public const TYPE_JPG = 'TYPE_JPG';
    final public const TYPE_JPEG = 'TYPE_JPEG';
    final public const TYPE_PNG = 'TYPE_PNG';
    final public const TYPE_GIF = 'TYPE_GIF';

    public function __construct(private string $src, private string $data, private string $type, private int $width, private int $height)
    {
    }

    public static function create(string $imagePath, string $type): self
    {
        $data = file_get_contents($imagePath);
        if (!$data) {
            throw new \Exception('Image cannot be read: '.$imagePath);
        }

        $imageSize = getimagesizefromstring($data);
        if (!$imageSize) {
            throw new \Exception('Image size is not a valid for image: '.$imagePath);
        }

        [$width, $height] = $imageSize;

        return new self($imagePath, $data, $type, $width, $height);
    }

    public function accept(DocumentVisitor $visitor): \Famoser\PdfGenerator\Backend\Structure\Document\Image
    {
        return $visitor->visitImage($this);
    }

    public function getIdentifier(): string
    {
        return $this->src;
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
