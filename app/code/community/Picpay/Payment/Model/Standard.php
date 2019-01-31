<?php

class Picpay_Payment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'picpay_standard';
    protected $_formBlockType = 'picpay_payment/form_picpay';
    protected $_infoBlockType = 'picpay_payment/info';
    protected $_isInitializeNeeded = false;
    
    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = true;
    protected $_canUseCheckout = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = false;
    protected $_order;

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        /** @var Picpay_Payment_Helper_Data $picpayHelper */
        $picpayHelper = Mage::helper('picpay_payment');
        
        $info->setAdditionalInformation('return_url', $picpayHelper->getReturnUrl());
        $info->setAdditionalInformation('mode_checkout', $picpayHelper->getModeCheckout());
        
        return $this;
    }

    /**
     * Consult transaction via API
     * 
     * @param Mage_Sales_Model_Order $order
     * @return bool|mixed|string
     */
	public function consultRequest($order)
	{
        //@TODO
        return false;
    }

    /**
     * Request cancel transaction via API
     * 
     * @param Mage_Sales_Model_Order $order
     * @return bool|mixed
     */
	public function cancelRequest($order)
	{
        //@TODO
        return false;
    }
}