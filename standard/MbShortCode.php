<?php

namespace MbAnnotation\standard;

use MocaBonita\MocaBonita;
use MocaBonita\tools\MbPage as Page;

/**
 * Specifies validation of a string, requiring a minimum and/or maximum length.
 *
 * @usage('method' => true, 'inherited' => false)
 */
class MbShortCode extends MbResource
{

    /**
     * Check if action is a shortcode
     *
     * @var bool
     */
    protected $shortcode = true;

    /**
     * @param MocaBonita $mocaBonita
     * @param Page       $mbPage
     * @param            $function
     *
     * @return \MocaBonita\tools\MbShortCode
     */
    public function mocaBonita(MocaBonita $mocaBonita, Page $mbPage, $function)
    {
        $shortcode = $mocaBonita->addMbShortcode($this->getName(), $mbPage, $this->getName());

        $shortcode->getMbAction()
            ->setFunctionName($function)
            ->setFunctionComplement("");

        return $shortcode;
    }
}