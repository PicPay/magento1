<?php

class Picpay_Payment_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'picpay_standard';
    protected $_formBlockType = 'picpay_payment/form_picpay';
    protected $_infoBlockType = 'picpay_payment/info';

    protected $_canOrder = true;

    protected $_isInitializeNeeded = false;
    protected $_isGateway  = false;
    protected $_canAuthorize = false;

    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = true;
    protected $_canUseCheckout = true;

    protected $_canCapture = false;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid = true;

    /** @var Picpay_Payment_Helper_Data $_helperPicpay */
    protected $_helperPicpay = null;


    public function getConfigPaymentAction()
    {
        return Mage_Payment_Model_Method_Abstract::ACTION_ORDER;
    }

    /**
     * Get PicPay Helper
     *
     * @return Picpay_Payment_Helper_Data
     */
    public function _getHelper()
    {
        if(is_null($this->_helperPicpay)) {
            $this->_helperPicpay = Mage::helper('picpay_payment');
        }
        return $this->_helperPicpay;
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return Picpay_Payment_Model_Standard
     * @throws Mage_Core_Exception
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        
        $info->setAdditionalInformation('return_url', $this->_getHelper()->getReturnUrl());
        $info->setAdditionalInformation('mode_checkout', $this->_getHelper()->getCheckoutMode());
        
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
    public function paymentRequest($order)
    {
        $data = array(
            'referenceId'   => $order->getIncrementId(),
            'callbackUrl'   => $this->_getHelper()->getCallbackUrl(),
            'returnUrl'     => $this->_getHelper()->getReturnUrl(),
            'value'         => round($order->getGrandTotal(), 2),
            'buyer'         => $this->_getHelper()->getBuyer($order)
        );

        $result = $this->_getHelper()->requestApi(
            $this->_getHelper()->getApiUrl("/payments"),
            $data
        );

        if(isset($result['success'])) {
            return $result;
        }

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
        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPayment();

        $authorizationId = $payment->getAdditionalInformation("authorizationId");

        $result = $this->_getHelper()->requestApi(
            $this->_getHelper()->getApiUrl("/payments/{$order->getIncrementId()}/cancellations"),
            array("authorizationId" => $authorizationId)
        );

        if(isset($result['success'])) {
            return $result;
        }

        return false;
    }

    /**
     * Authorize payment picpay_standard method
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @throws Mage_Core_Exception
     *
     * @return Picpay_Payment_Model_Standard
     */
    public function order(Varien_Object $payment, $amount)
    {
        parent::order($payment, $amount);

        /** @var Mage_Sales_Model_Order $order */
        $order = $payment->getOrder();

        $this->_getHelper()->log("Order Model Payment Method");

        $return = $this->paymentRequest($order);

        if(!is_array($return)) {
            Mage::throwException($this->_getHelper()->__('Unable to process payment. Contact Us.'));
        }
        if($return['success'] == 0) {
            Mage::throwException($this->_getHelper()->__($return['return']));
        }

        try {
            $payment->setAdditionalInformation("paymentUrl", $return["return"]["paymentUrl"]);
            $payment->save();
        }
        catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Void payment picpay_standard method
     *
     * @param Varien_Object $payment
     * @throws Mage_Core_Exception
     *
     * @return Picpay_Payment_Model_Standard
     */
    public function void(Varien_Object $payment)
    {
        parent::void($payment);

        /** @var Mage_Sales_Model_Order $order */
        $order = $payment->getOrder();

        $this->_getHelper()->log("Void Model Payment Method");

        $return = $this->cancelRequest($order);

        if(!is_array($return)) {
            Mage::throwException($this->_getHelper()->__('Unable to process payment. Contact Us.'));
        }
        if($return['success'] == 0) {
            Mage::throwException($this->_getHelper()->__($return['return']));
        }

        try {
            $payment->setAdditionalInformation("cancellationId", $return["return"]["cancellationId"]);
            $payment->save();
        }
        catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Refund specified amount for picpay_standard payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @throws Mage_Core_Exception
     *
     * @return Picpay_Payment_Model_Standard
     */
    public function refund(Varien_Object $payment, $amount)
    {
        parent::refund($payment, $amount);

        /** @var Mage_Sales_Model_Order $order */
        $order = $payment->getOrder();

        $this->_getHelper()->log("Refund Model Payment Method");

        $return = $this->cancelRequest($order);

        if(!is_array($return)) {
            Mage::throwException($this->_getHelper()->__('Unable to process payment. Contact Us.'));
        }
        if($return['success'] == 0) {
            Mage::throwException($this->_getHelper()->__($return['return']));
        }

        try {
            $payment->setAdditionalInformation("cancellationId", $return["return"]["cancellationId"]);
            $payment->save();
        }
        catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }
}