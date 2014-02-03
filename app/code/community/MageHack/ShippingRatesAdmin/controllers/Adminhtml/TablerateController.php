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
        
        $id  = $this->getRequest()->getParam('id');
        $shippingrate = new Varien_Object(); //Mage::getModel('shipping/carrier_tablerate');
        
        if ($id) {
            // Load record
            
            $resource = Mage::getResourceModel('shipping/carrier_tablerate');
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $binds = array( 'id' => $id);
            $query = "select * FROM {$resource->getMainTable()} WHERE `pk` = :id";
            $data = $adapter->fetchRow($query, $binds);
            
            
     
            // Check if record is loaded
            if (!$data) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Shipping Rate no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            } else {
                $shippingrate->setData($data);
            }   
        } else {
            // for new rates set the default country to the country of the store
            $shippingrate->setData('dest_country_id',Mage::getStoreConfig('general/country/default'));
            
            $shippingrate->setData('website_id',Mage::app()->getRequest()->getParam('website'));
        }
        
        Mage::register('shippingrate', $shippingrate);
        $this->renderLayout();
    }
    
    public function regionAction() {
        $countrycode = $this->getRequest()->getParam('country');
        $state = "<option value=''>Please Select</option>";
        if ($countrycode != '') {
            $statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($countrycode)->load();
            if($statearray->getSize() == 0)
              $state = "<option value='0'>" . $this->__('No regions defined for this country.') . "</option>";  
            
            foreach ($statearray as $_state) {
                $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
            }
        }
        $this->getResponse()->setBody($state);
    }
    
    
    public function saveAction()
    {
        if($this->getRequest()->getPost())  {
           try {
                $data = $this->getRequest()->getPost();
         
                $binds = array( 
                                    'website_id' => $data['website_id'],
                                    'dest_country_id' => $data['dest_country_id'],
                                    'dest_region_id' => $data['dest_region_id'],
                                    'dest_zip' => $data['dest_zip'],
                                    'condition_value' => $data['condition_value'],
                                    'price' => $data['price'],
                                    'condition_name' => Mage::getStoreConfig('carriers/tablerate/condition_name')
                                    );
                
                
                $resource = Mage::getResourceModel('shipping/carrier_tablerate');
                $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');;
                

                if(isset($data['pk']))
                {
                    $binds['pk'] = $data['pk'];
                    // Update existing
                    $query = "UPDATE {$resource->getMainTable()} "
                            . "SET `website_id` = :website_id, "
                            . "`dest_country_id` = :dest_country_id, "
                            . "`dest_region_id` = :dest_region_id, "
                            . "`dest_zip` = :dest_zip, "
                            . "`condition_value` = :condition_value, "
                            . "`condition_name` = :condition_name, "
                            . "`price` = :price "
                            . "WHERE `pk` = :pk ";
                    
    
                    $adapter->query($query, $binds);

                }else{
                   
                    // Add New
                    $query = "insert into {$resource->getMainTable()} (website_id, dest_country_id, dest_region_id, dest_zip, condition_value, condition_name, price) "
                    . "values (:website_id, :dest_country_id, :dest_region_id, :dest_zip, :condition_value, :condition_name, :price)";                   
                    
                    
                    $adapter->query($query, $binds);
                }
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Shipping Rate has been saved.'));
                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('pk')));
                } else {
                    $this->_redirect('*/*/', array('website' => $data['website_id']));
                }
 
                return;
            }   
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this Shipping Rate.'. $e->getMessage()));
            } 
            
            $this->_redirectReferer();
        }
    }
    
    /**
     * Fired when you change the website selected in the editing form to return
     * website specific configuration that may affect fields in the form such as 
     * the condition (weight/destination, price/destination...)
     */
    public function changewebsiteAction() {
        $website = $this->getRequest()->getParam('website');
        $conditionLabel = $label = Mage::getSingleton('shipping/carrier_tablerate')
            ->getCode('condition_name_short', $this->_getShippingRatesAdminHelper()->getWebsiteConfigData('carriers/tablerate/condition_name', $website))
            . " <span class=\"required\">*</span>";
        $data = array('conditionLabel' => $conditionLabel);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($data));        
    }
    
    /**
     * 
     * @return MageHack_ShippingRatesAdmin_Helper_Data
     */
    protected function _getShippingRatesAdminHelper() {
        return Mage::helper('shippingratesadmin');
    }
    
}
