<?php

/*
 * This file is part of the famoser/pdf-generator project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PdfGenerator\Backend;

use PdfGenerator\Backend\Token\ArrayToken;
use PdfGenerator\Backend\Token\Base\BaseToken;
use PdfGenerator\Backend\Token\DictionaryToken;
use PdfGenerator\Backend\Token\NumberToken;
use PdfGenerator\Backend\Token\ReferenceToken;
use PdfGenerator\Backend\Token\TextToken;

class TokenVisitor
{
    /**
     * @param ArrayToken $token
     *
     * @return string
     */
    public function visitArrayToken(ArrayToken $token): string
    {
        return implode(' ', $this->evaluateTokenArray($token->getValues()));
    }

    /**
     * @param NumberToken $token
     *
     * @return float
     */
    public function visitNumberToken(NumberToken $token): string
    {
        return $token->getNumber();
    }

    /**
     * @param DictionaryToken $token
     *
     * @return string
     */
    public function visitDictionaryToken(DictionaryToken $token): string
    {
        $entries = '';
        $evaluatedTokens = $this->evaluateTokenArray($token->getKeyValue());
        foreach ($evaluatedTokens as $key => $value) {
            $entries .= '/' . $key . ' ' . $value;
        }

        return '<<' . $entries . '>>';
    }

    /**
     * @param ReferenceToken $token
     *
     * @return string
     */
    public function visitReferenceToken(ReferenceToken $token): string
    {
        return $token->getTarget()->getNumber() . ' 0 R';
    }

    /**
     * @param TextToken $token
     *
     * @return string
     */
    public function visitTextToken(TextToken $token): string
    {
        return $token->getText();
    }

    /**
     * @param BaseToken[] $tokens
     *
     * @return string[]
     */
    private function evaluateTokenArray(array $tokens): array
    {
        $entries = [];
        foreach ($tokens as $key => $token) {
            $entries[$key] = $token->accept($this);
        }

        return $entries;
    }
}