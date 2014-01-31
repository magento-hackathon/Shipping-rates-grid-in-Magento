<?php

class MageHack_ShippingRatesAdmin_Block_Adminhtml_Tablerate extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_tablerate';
        $this->_blockGroup = 'shippingratesadmin';
        $this->_headerText = Mage::helper('shippingratesadmin')->__('Table Rates');
        $this->_addButtonLabel = Mage::helper('shippingratesadmin')->__('Add New RAte');
        parent::__construct();
    }

}
