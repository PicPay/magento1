<?php

class Picpay_Payment_Model_Source_Customer_Taxvat
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Picpay_Payment_Helper_Data $picpayHelper */
        $picpayHelper = Mage::helper('picpay');
        $fields = $picpayHelper->getFields('customer');

        $options = array();
        $options[] = array(
            'value' => '',
            'label' => $picpayHelper->__('Solicitar junto com os outros dados do pagamento')
        );
        foreach ($fields as $key => $value) {
            if (!is_null($value['frontend_label'])) {
                $options['customer|'.$value['frontend_label']] = array(
                    'value' => 'customer|'.$value['attribute_code'],
                    'label' => 'Customer: '.$value['frontend_label'] . ' (' . $value['attribute_code'] . ')'
                );
            }
        }

        $addressFields = $picpayHelper->getFields('customer_address');
        foreach ($addressFields as $key => $value) {
            if (!is_null($value['frontend_label'])) {
                $options['address|'.$value['frontend_label']] = array(
                    'value' => 'billing|'.$value['attribute_code'],
                    'label' => 'Billing: '.$value['frontend_label'] . ' (' . $value['attribute_code'] . ')'
                );
            }
        }

        return $options;
    }
}