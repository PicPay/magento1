<?php
class Picpay_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get fields from a given entity
     * @author Gabriela D'Ávila (http://davila.blog.br)
     * @param $type
     * @return mixed
     */
    public function getFields($type = 'customer_address')
    {
        $entityType = Mage::getModel('eav/config')->getEntityType($type);
        $entityTypeId = $entityType->getEntityTypeId();
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($entityTypeId);
        return $attributes->getData();
    }
}