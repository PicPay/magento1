<?php   
class Picpay_Payment_Block_Info extends Mage_Payment_Block_Info
{
    protected $_order = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('picpay/info.phtml');
    }

    /**
     * Get order object instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder() {
        if(!$this->_order) {
            $this->_order = Mage::registry('current_order');
            if (!$this->_order) {
                $info = $this->getInfo();
                if ($this->getInfo() instanceof Mage_Sales_Model_Order_Payment) {
                    $this->_order = $this->getInfo()->getOrder();
                }
            }
        }
		return $this->_order;
    }

    public function getPaymentUrl()
    {
        $order = $this->getOrder();
        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPayment();

        return $payment->getAdditionalInformation("paymentUrl");
    }
}