<?php

class Picpay_Payment_Model_Source_Mode
{
    public function toOptionArray()
    {
        /** @var Picpay_Payment_Helper_Data $picpayHelper */
        $picpayHelper = Mage::helper('picpay_payment');

        return array(
            array('value' => $picpayHelper::ONPAGE_MODE, 'label' => 'On Page'),
            array('value' => $picpayHelper::IFRAME_MODE, 'label' => 'Iframe'),
            array('value' => $picpayHelper::REDIRECT_MODE, 'label' => 'Redirect')
        );
    }
}
