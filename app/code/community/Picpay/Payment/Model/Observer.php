<?php 

class Picpay_Payment_Model_Observer extends Varien_Event_Observer
{
    protected function _cancelOrder($order, $helper)
    {
        $payment = $order->getPayment();
        $return = $payment->getMethodInstance()->cancelRequest($order);

        $helper->log("Cancel Order Return");
        $helper->log($return);

        if(!is_array($return)) {
            Mage::getSingleton('core/session')->addError($helper->__('Error while try refund order.'));
            return $this;
        }
        if($return['success'] == 0) {
            Mage::getSingleton('core/session')->addError($helper->__('Error while try refund order.') . " " . $return['return']);
            return $this;
        }

        try {
            $payment->setAdditionalInformation("cancellationId", $return["return"]["cancellationId"]);
            $payment->save();
            Mage::getSingleton('core/session')->addSuccess($helper->__('Order canceled with success at Picpay.'));
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($helper->__('Error while try refund order. '. $e->getMessage()));
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Cancel transacion via API PicPay by cancel order save event
     */
    public function cancelTransaction($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var Picpay_Payment_Helper_Data $helper */
        $helper = Mage::helper("picpay_payment");

        if(!$order
            || !$order->getId()
            || $order->getPayment()->getMethodInstance()->getCode() != "picpay_standard"
        ) {
            return $this;
        }

        return $this->_cancelOrder($order, $helper);
    }

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

        if(!$order
            || !$order->getId()
            || $order->getPayment()->getMethodInstance()->getCode() != "picpay_standard"
        ) {
            return $this;
        }

        return $this->_cancelOrder($order, $helper);
    }

    /**
     * Add button to actions on Order View
     *
     * @param $observer
     */
    public function addOrderButtonsAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $message = Mage::helper('picpay_payment')->__('Are you sure you want to Sync Picpay Transaction?');

            $order = Mage::registry("sales_order");

            if($order && $order->getId()) {
                $block->addButton('picpay_sync',
                    array(
                        'label' => Mage::helper('picpay_payment')->__('Sync Picpay Transaction'),
                        'onclick' => "confirmSetLocation('{$message}', '{$block->getUrl('picpay/adminhtml_index/consult?order_id='.($order->getId()))}')",
                        'class' => 'go'
                    )
                );
            }
        }
    }
}