<?php

class MageHack_ShippingRatesAdmin_Block_Adminhtml_Tablerate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Shipping_Model_Mysql4_Carrier_Tablerate_Collection */
        $collection = Mage::getResourceModel('shipping/carrier_tablerate_collection');
        $collection->setConditionFilter(Mage::getStoreConfig('carriers/tablerate/condition_name'));
        
        $site = Mage::app()->getRequest()->getParam('website');
        $collection->setWebsiteFilter($site);
        
        
        $this->setCollection($collection);
        
        

        return parent::_prepareCollection();
    }
    
    /**
     * Prepare table columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
                
        $site = Mage::getModel('core/website')->load(Mage::app()->getRequest()->getParam('website'));
        $store = $site->getDefaultStore();
        $countries = Mage::getResourceModel('directory/country_collection')->loadByStore($store->getId());
        
        
        $country_options = array();
        foreach($countries as $country)
        {
            
         $country_options[$country->getData('iso3_code')] =    $country->getName();
        }
        
        $this->addColumn('dest_country', array(
            'header'    => Mage::helper('adminhtml')->__('Country'),
            'index'     => 'dest_country',
            'default'   => '*',
            'type'     => 'options',
            'options'   =>$country_options
        ));

        $this->addColumn('dest_region', array(
            'header'    => Mage::helper('adminhtml')->__('Region/State'),
            'index'     => 'dest_region',
            'default'   => '*',
        ));

        $this->addColumn('dest_zip', array(
            'header'    => Mage::helper('adminhtml')->__('Zip/Postal Code'),
            'index'     => 'dest_zip',
            'default'   => '*',
        ));

        $label = Mage::getSingleton('shipping/carrier_tablerate')
            ->getCode('condition_name_short',Mage::getStoreConfig('carriers/tablerate/condition_name'));
        Mage::getSingleton('adminhtml/system_config_source_shipping_tablerate');
        
        $this->addColumn('condition_value', array(
            'header'    => $label,
            'index'     => 'condition_value',
            'type'  => 'number',
            
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('adminhtml')->__('Shipping Price'),
            'index'     => 'price',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getPk()));
    }
    

}