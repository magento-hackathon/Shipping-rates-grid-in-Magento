<?php

class MageHack_ShippingRatesAdmin_Adminhtml_TablerateController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('system/shippingratesadmin');
        return $this;
    }   
 
    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }
    
    public function gridAction() {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('shippingratesadmin/adminhtml_tablerate_grid')->toHtml()
        );
    }
    
    /**
     * Add New Rate action
     */
    public function newAction() {
	$this->_forward('edit');
		
    }
    
    public function editAction() {
        $this->_initAction();
        $this->renderLayout();
    }
    
}
