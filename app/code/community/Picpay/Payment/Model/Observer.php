<?php 

class Picpay_Payment_Model_Observer extends Varien_Event_Observer
{
    /**
     * This is an observer function for the event 'controller_front_init_before'.
     * It prepends our autoloader, so we can load the extra libraries.
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerFrontInitBefore(/** @noinspection PhpUnusedParameterInspection */ $observer)
    {
        /** @var JeroenVermeulen_Solarium_Helper_Autoloader $autoLoader */
        $autoLoader = Mage::helper('picpay_payment/autoloader');
        $autoLoader->register();
    }

    /**
     * Cancel payment transaction in PicPay api
     *
     * @param Mage_Sales_Model_Order $order
     * @param Picpay_Payment_Helper_Data $helper
     * @throws Mage_Core_Exception
     * @return $this
     */
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
     *
     * @param $observer
     * @throws Mage_Core_Exception
     * @return Picpay_Payment_Model_Observer
     */
    public function cancelTransaction($observer)
    {
        /** @var Picpay_Payment_Helper_Data $helper */
        $helper = Mage::helper("picpay_payment");

        if(!$helper->isModuleEnabled()) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

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
     *
     * @param $observer
     * @throws Mage_Core_Exception
     * @return Picpay_Payment_Model_Observer
     */
    public function refundTransaction($observer)
    {
        /** @var Picpay_Payment_Helper_Data $helper */
        $helper = Mage::helper("picpay_payment");

        if(!$helper->isModuleEnabled()) {
            return $this;
        }

        $creditmemo = $observer->getEvent()->getCreditmemo();

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($creditmemo->getOrderId());

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
     * @return Picpay_Payment_Model_Observer
     */
    public function addOrderButtonsAction($observer)
    {
        /** @var Picpay_Payment_Helper_Data $helper */
        $helper = Mage::helper("picpay_payment");

        if(!$helper->isModuleEnabled()) {
            return $this;
        }

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

    /**
     * Add qrcode block when mode is appropriate
     *
     * @param $observer
     * @return Picpay_Payment_Model_Observer
     */
    public function addPicpayQrcodeBlock($observer)
    {
        /** @var Picpay_Payment_Helper_Data $helper */
        $helper = Mage::helper("picpay_payment");

        if(!$helper->isModuleEnabled()
            || !$helper->isActive()
            || $helper->isRedirectMode()
        ) {
            return $this;
        }

        /** @var $_block Mage_Core_Block_Abstract */
        $_block = $observer->getBlock();
        $session = Mage::getSingleton('checkout/session');
        /** @var Mage_Core_Model_Layout $layout */
        $layout = Mage::app()->getLayout();
        $handles = $layout->getUpdate()->getHandles();

        if ($_block->getType() == 'core/text_list'
            && $_block->getNameInLayout() == "content"
            && $session->getLastOrderId()
            && (
                in_array("checkout_onepage_success", $handles) == true ||
                in_array("checkout_multishipping_success", $handles) == true
            )
        ) {
            $template = $helper::PHTML_SUCCESS_PATH_IFRAME;
            if($helper->isOnpageMode()) {
                $template = $helper::PHTML_SUCCESS_PATH_ONPAGE;
            }

            $picpayBlock = $layout->createBlock(
                'Mage_Core_Block_Template',
                'picpay.qrcode.success',
                array('template' => $template)
            );
            $_block->append($picpayBlock);
        }
    }
}