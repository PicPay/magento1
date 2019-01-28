<?php

class Picpay_Payment_Model_Source_Mode
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => 'Iframe'),
            array('value' => '2', 'label' => 'Redirect')
        );
    }
}
