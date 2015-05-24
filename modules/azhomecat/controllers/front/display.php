<?php

class AzHomeCatDisplayModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('azhomecat.tpl');
    }
}
