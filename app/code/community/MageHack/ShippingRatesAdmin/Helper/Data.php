<?php

class MageHack_ShippingRatesAdmin_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * 
     * @param string $path
     * @param mixed $website
     * @return string
     */
    public function getWebsiteConfigData($path, $website) {
        $value = Mage::app()->getWebsite($website)->getConfig($path);
        return $value;        
    }
}
