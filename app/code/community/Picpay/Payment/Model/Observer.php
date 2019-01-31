<?php 

class Picpay_Payment_Model_Observer extends Varien_Event_Observer
{
    /**
     * Refund transacion via API PicPay by creditmemo save event
     */
    public function refundTransaction($observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($creditmemo->getOrderId());
        
        /** @var Picpay_Payment_Helper_Data $helper */
        $helper = Mage::helper("picpay_payment");

        if( !$order 
			|| !$order->getId() 
			|| $order->getPayment()->getMethodInstance()->getCode() != "picpay_standard"
        ) {
			return;
        }

        if($order->getPayment()->getMethodInstance()->cancelRequest($order, $creditmemo->getGrandTotal())) {
			Mage::getSingleton('core/session')->addSuccess($helper->__('Order refund with success at Picpay.')); 
		}
		else {
			Mage::getSingleton('core/session')->addError($helper->__('Error while try refund order.')); 
		}
    }
}