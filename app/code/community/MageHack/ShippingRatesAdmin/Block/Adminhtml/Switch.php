<?php

class MageHack_ShippingRatesAdmin_Block_Adminhtml_Switch extends Mage_Adminhtml_Block_Widget_Form
{
    /** 
     * Init class
     */
    public function __construct()
    {   
        parent::__construct();
     
        $this->setId('shippingrate_switch');
        $this->setTitle($this->__('Select Website'));
    }
    
    /** 
     * Setup form fields for inserts/updates
     * 
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {   
       
        
     
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/*', array('website' => $this->getRequest()->getParam('website'))),
            'method'    => 'get',
            
        )); 
        
        $site = Mage::app()->getRequest()->getParam('website');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Select Website'),
            'class'     => 'fieldset-wide',
            
        )); 
     

        
        $switch = $fieldset->addField('website_id', 'select', array(
                'name'      => 'website_id',
                'label'     => Mage::helper('shippingratesadmin')->__('Website'),
                'title'     => Mage::helper('shippingratesadmin')->__('Website'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm(true, false),
                'value'     => $site,
                'onchange' => 'switchStore(this)',
            ));
        $switch->setAfterElementHtml(
        "<script type=\"text/javascript\">
            function switchStore(selectElement){
                var reloadurl = '". $this->getUrl('shippingratesadmin/adminhtml_tablerate/index') . "website/' + selectElement.value;
                window.location.href = reloadurl;
            }
        </script>");

        $form->setUseContainer(false);
        $this->setForm($form);
     
        return parent::_prepareForm();
    } 
}