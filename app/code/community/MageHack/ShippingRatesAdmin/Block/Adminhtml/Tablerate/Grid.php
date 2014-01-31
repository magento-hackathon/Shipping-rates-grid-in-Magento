<?php

class MageHack_ShippingRatesAdmin_Block_Adminhtml_Tablerate_Grid extends Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid
{

    
    /**
     * Prepare table columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('dest_country', array(
            'header'    => Mage::helper('adminhtml')->__('Country'),
            'index'     => 'dest_country',
            'default'   => '*',
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

        $label = Mage::getStoreConfig('carriers/tablerate/condition_name');
        
        $this->addColumn('condition_value', array(
            'header'    => $label,
            'index'     => 'condition_value',
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('adminhtml')->__('Shipping Price'),
            'index'     => 'price',
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}