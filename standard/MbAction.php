<?php

namespace MbAnnotation\standard;

use MocaBonita\tools\MbPage as Page;

/**
 * Specifies validation of a string, requiring a minimum and/or maximum length.
 *
 * @usage('method' => true, 'inherited' => false)
 */
class MbAction extends MbResource
{
    /**
     * @param Page $mbPage
     *
     * @return \MocaBonita\tools\MbAction
     */
    public function mocaBonita(Page $mbPage, $function)
    {
        $mbAction = $mbPage->addMbAction($this->getName());

        $mbAction->setCapability($this->getCapability())
            ->setRequiresLogin($this->isLogin())
            ->setRequiresMethod($this->getMethod())
            ->setRequiredParams($this->getParams())
            ->setRules($this->getRules())
            ->setFunctionName($function)
            ->setFunctionComplement("");

        return $mbAction;
    }
}