<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend\Object;

use PdfGenerator\Backend\Object\Base\BaseObject;
use PdfGenerator\Backend\ObjectVisitor;
use PdfGenerator\Backend\Token\DictionaryToken;
use PdfGenerator\Backend\Token\NumberToken;

class StreamObject extends BaseObject
{
    /**
     * @var string
     */
    private $content;

    /**
     * StreamObject constructor.
     *
     * @param int $number
     * @param string $content
     */
    public function __construct(int $number, string $content)
    {
        parent::__construct($number);
        $this->content = $content;
    }

    /**
     * @param ObjectVisitor $visitor
     *
     * @return string
     */
    public function accept(ObjectVisitor $visitor): string
    {
        return $visitor->visitStream($this);
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return DictionaryToken
     */
    public function getMetaData(): DictionaryToken
    {
        $dictionary = new DictionaryToken();
        $dictionary->setEntry('Length', new NumberToken(mb_strlen($this->content)));

        return $dictionary;
    }
}