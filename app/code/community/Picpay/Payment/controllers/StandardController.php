<?php
class Picpay_Payment_StandardController extends Mage_Core_Controller_Front_Action{

    /**
     * Expired Session
     */
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    public function redirectAction()
    {
        
    }
}