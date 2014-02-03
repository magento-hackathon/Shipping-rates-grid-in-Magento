<?php

class MageHack_ShippingRatesAdmin_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get configuration value for website.
     * @param string $path
     * @param mixed $website
     * @return string
     */
    public function getWebsiteConfigData($path, $website) {
        $value = Mage::app()->getWebsite($website)->getConfig($path);
        return $value;        
    }
    
    /**
     * Save shipping rate data in session.
     * @param array $data
     * @return \MageHack_ShippingRatesAdmin_Helper_Data
     */
    public function setShippingRateSessionData($data) {
        Mage::getSingleton('adminhtml/session')->setShippingAdminRateData($data);
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getShippingRateSessionData() {
        $data = Mage::getSingleton('adminhtml/session')->getShippingAdminRateData(null);
        return $data;
    }
    
    /**
     * 
     * @return \MageHack_ShippingRatesAdmin_Helper_Data
     */
    public function clearShippingRateSessionData() {
        $this->setShippingRateSessionData(null);
        return $this;
    }
}
