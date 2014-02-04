<?php


class  MageHack_ShippingRatesAdmin_Block_Adminhtml_Tablerate_Import_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * @var array
     */
    public function __construct() {
        parent::__construct();
    }    
    
   protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/importrates'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ));
        $this->setForm($form);


        $fieldset = $form->addFieldset('base_fieldset',array());

        $siteId = $this->getRequest()->getParam('website');
        $fieldset->addField('website_id', 'select', array(
            'name' => 'website_id',
            'label' => $this->_getHelper()->__('Website'),
            'values' => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true, false),
            'required' => true,
            'value' => $siteId
        ));         
        
        $fieldset->addField('import', 'file', array(
            'name' => 'import',
            'label' => $this->_getHelper()->__('Import Rates'),
            'required' => true,
            
        ));        
        

        
        
        
        

        $form->setUseContainer(true);

        return parent::_prepareForm();
    }


    /**
     * 
     * @return MageHack_ShippingRatesAdmin_Helper_Data
     */
    protected function _getHelper() {
        return Mage::helper('shippingratesadmin');
    }

}

?>
