<?php

class Picpay_Payment_Model_Source_Customer_Address
{
    /**
     * Return Address attribute
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Picpay_Payment_Helper_Data $picpayHelper */
        $picpayHelper = Mage::helper('picpay');
        $fields = $picpayHelper->getFields('customer_address');
        $options = array();

        foreach ($fields as $key => $value) {
            if (!is_null($value['frontend_label'])) {
                if ($value['attribute_code'] == 'street') {
                    $streetLines = Mage::getStoreConfig('customer/address/street_lines');
                    for ($i = 1; $i <= $streetLines; $i++) {
                        $options[] = array('value' => 'street_'.$i, 'label' => 'Street Line '.$i);
                    }
                } else {
                    $options[] = array(
                        'value' => $value['attribute_code'],
                        'label' => $value['frontend_label'] . ' (' . $value['attribute_code'] . ')'
                    );
                }
            }
        }
        return $options;
    }
}