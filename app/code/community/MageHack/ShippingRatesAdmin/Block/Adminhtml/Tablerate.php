<?php

class MageHack_ShippingRatesAdmin_Block_Adminhtml_Tablerate extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Don't show if no website is selected
     * @return string
     */
    protected function _toHtml(){
        if(!Mage::app()->getRequest()->getParam('website'))
            return '';
        else
            return parent::_toHtml();
    }

    public function __construct()
    {
        $this->_controller = 'adminhtml_tablerate';
        $this->_blockGroup = 'shippingratesadmin';
        $this->_headerText = Mage::helper('shippingratesadmin')->__('Table Rates');
        $this->_addButtonLabel = Mage::helper('shippingratesadmin')->__('Add New Rate');
        parent::__construct();
        $this->_removeButton('add');
        

        $site_id = Mage::app()->getRequest()->getParam('website');
        
        
        if($site_id){
            $site_code = Mage::getModel('core/website')->load($site_id)->getCode();

            $export_url = $this->getUrl('adminhtml/system_config/exportTablerates', 
                                                array(  'website' => $site_code, 
                                                        'conditionName' => $this->_getHelper()->getWebsiteConfigData('carriers/tablerate/condition_name', $site_id)
                                                    )
                                        );


            $this->_addButton('export_rates', array(
                    'class'   => 'export',
                    'label'   => Mage::helper('shippingratesadmin')->__('Export'),
                    'onclick' => "setLocation('$export_url')",
                ), 4);
   
            $import_url = $this->getUrl('*/*/import', 
                                                array(  'website' => $site_id)
                                        );            
            $this->_addButton('import_rates', array(
                    'class'   => 'import',
                    'label'   => Mage::helper('shippingratesadmin')->__('Import'),
                    'onclick' => "setLocation('$import_url')",
                ), 4);            
            
        
            $add_url = $this->getUrl('*/*/new', array(  'website' => $site_id) );

            $this->_addButton('add', array(
                        'class'   => 'add',
                        'label'   => Mage::helper('shippingratesadmin')->__('Add New Rate'),
                        'onclick' => "setLocation('$add_url')",
                    ), 4);
        }
        

        
        
        
    }
    
    /**
     * 
     * @return MageHack_ShippingRatesAdmin_Helper_Data
     */
    protected function _getHelper() {
        return Mage::helper('shippingratesadmin');
    }    

}
