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

    public function getQrcodeSource()
    {
        $order = $this->getOrder();
        if(is_null($order)) {
            return "";
        }

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPayment();

        return $payment->getAdditionalInformation("qrcode");
    }

    public function getPaymentUrl()
    {
        $order = $this->getOrder();
        if(is_null($order)) {
            return "";
        }

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPayment();

        return $payment->getAdditionalInformation("paymentUrl");
    }

    public function getCancellationId()
    {
        $order = $this->getOrder();
        if(is_null($order)) {
            return "";
        }

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPayment();

        return $payment->getAdditionalInformation("cancellationId");
    }

    public function getAuthorizationId()
    {
        $order = $this->getOrder();
        if(is_null($order)) {
            return "";
        }

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPayment();

        return $payment->getAdditionalInformation("authorizationId");
    }

    public function getQrcode()
    {
        if($qrcodeSource = $this->getQrcodeSource()) {
            /** @var Picpay_Payment_Helper_Data $picpayHelper */
            $picpayHelper = Mage::helper("picpay_payment");

            $imageSize = $picpayHelper->getQrcodeInfoWidth()
                ? $picpayHelper->getQrcodeInfoWidth()
                : $picpayHelper::DEFAULT_QRCODE_WIDTH
            ;

            return $picpayHelper->generateQrCode($qrcodeSource, $imageSize);
        }
    }
}