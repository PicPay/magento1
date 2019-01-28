<?php

class Picpay_Payment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'picpay_standard';
    protected $_formBlockType = 'picpay/form_picpay';
    protected $_infoBlockType = 'picpay/info';
    protected $_isInitializeNeeded = false;
    
    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = true;
    protected $_canUseCheckout = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = false;
    protected $_order;


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