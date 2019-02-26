<?php

class Picpay_Payment_Block_Form_Picpay extends Mage_Payment_Block_Form
{
    /**
     * Especifica template.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('picpay/form/picpay.phtml');
    }
}