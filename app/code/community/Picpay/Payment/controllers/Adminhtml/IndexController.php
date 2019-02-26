<?php

class Picpay_Payment_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function consultAction()
    {
        /** @var Picpay_Payment_Helper_Data $helper */
        $helper = Mage::helper("picpay_payment");

        $orderId = $this->getRequest()->getParam("order_id");

        if(!$orderId) {
            $this->_redirectReferer();
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel("sales/order")->load($orderId);

        if(!$order
            || !$order->getId()
            || $order->getPayment()->getMethodInstance()->getCode() != "picpay_standard"
        ) {
            $this->_redirectReferer();
            return;
        }

        $return = $order->getPayment()->getMethodInstance()->consultRequest($order);

        if(!is_array($return) || $return['success'] == 0) {
            $this->_redirectReferer();
            return;
        }

        $authorizationId = $order->getPayment()->getAdditionalInformation("authorizationId");

        $helper->updateOrder($order, $return, $authorizationId);

        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderId));
    }
}