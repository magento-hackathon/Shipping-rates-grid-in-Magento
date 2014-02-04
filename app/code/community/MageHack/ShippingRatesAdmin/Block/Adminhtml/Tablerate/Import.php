<?php

/**
 * Description of Importexport
 *
 * @author davidslater
 */
class MageHack_ShippingRatesAdmin_Block_Adminhtml_Tablerate_Import extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_tablerate';
        $this->_blockGroup = 'shippingratesadmin';
        $this->_mode = 'import';
        parent::__construct();

        $this->_updateButton('save',  'label', $this->_getHelper()->__('Import'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getHeaderText() {
        return $this->_getHelper()->__('Import Tablerates');
    }

    public function getBackUrl() {
        return $this->getUrl('*/*/');
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
