<?php 

class Picpay_Payment_Model_Observer extends Varien_Event_Observer
{
    /**
     * Refund transacion via API PicPay by creditmemo save event
     */
    public function refundTransaction($observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = Mage::getModel('sales/order')->load($creditmemo->getOrderId());
        $helper = Mage::helper("picpay");

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