<?php

class MageHack_ShippingRatesAdmin_Block_Adminhtml_Tablerate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_tablerate';
        $this->_blockGroup = 'shippingratesadmin';
        parent::__construct();

        
        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => Mage::helper('shippingratesadmin')->__('Save and Continue Edit'),
            'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
        ), 10);
    }
    
    /** 
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {   
        if (Mage::registry('shippingrate') && Mage::registry('shippingrate')->getPk()) {
            return $this->__('Edit Shipping Rate');
        }   
        else {
            return $this->__('New Shipping Rate');
        }   
    } 
    
}