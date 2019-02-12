<?php

class Picpay_Payment_Helper_Autoloader extends Mage_Core_Helper_Abstract
{
    /**
     * Prepends our autoloader, so we can load the extra library classes when they are needed.
     */
    public function register()
    {
        $registryKey = 'Picpay_Autoloader_registered';
        if (!Mage::registry( $registryKey )) {
            Mage::register( $registryKey, true );
            spl_autoload_register( array( $this, 'load' ), true, true );
        }
    }

    /**
     * This function can autoload classes starting with
     *
     * @param string $class
     */
    public static function load($class)
    {
        require_once Mage::getBaseDir( 'lib' ) . DS . "phpqrcode" . DS . "qrlib.php";
    }
}