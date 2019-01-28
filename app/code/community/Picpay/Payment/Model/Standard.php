<?php

class Picpay_Payment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'picpay';
    protected $_formBlockType = 'picpay/form_picpay';
    protected $_infoBlockType = 'picpay/info';
    protected $_isInitializeNeeded = false;
    
    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = true;
    protected $_canUseCheckout = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = false;
    protected $_order;
    
}