<?php

class MageHack_ShippingRatesAdmin_Adminhtml_TablerateController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('system/shippingratesadmin');
        return $this;
    }

    public function indexAction() {
        // If loading the page without a site specified, use the last site 
        // specified so the user doesn't have to keep selecting.
        $siteId = Mage::app()->getRequest()->getParam('website');
        if (is_null($siteId)) {
            $siteId = Mage::getSingleton('admin/session')->getShippingRatesAdminSiteId();
        }
        $this->getRequest()->setParam('website', $siteId);
        Mage::getSingleton('admin/session')->setShippingRatesAdminSiteId($siteId);


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
        $this->_getShippingRatesAdminHelper()->clearShippingRateSessionData();
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $shippingrate = new Varien_Object(); //Mage::getModel('shipping/carrier_tablerate');

        if ($id) {
            // Load record

            $resource = Mage::getResourceModel('shipping/carrier_tablerate');
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
            $binds = array('id' => $id);
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
            $shippingrate->setData('dest_country_id', Mage::getStoreConfig('general/country/default'));

            $shippingrate->setData('website_id', Mage::app()->getRequest()->getParam('website'));
        }

        Mage::register('shippingrate', $shippingrate);

        $this->_initAction();
        $this->renderLayout();
    }

    public function regionAction() {
        $countrycode = $this->getRequest()->getParam('country');
        $state = "<option value=''>Please Select</option>";
        if ($countrycode != '') {
            $statearray = Mage::getModel('directory/region')->getResourceCollection()->addCountryFilter($countrycode)->load();
            if ($statearray->getSize() == 0)
                $state = "<option value='0'>" . $this->__('No regions defined for this country.') . "</option>";

            foreach ($statearray as $_state) {
                $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
            }
        }
        $this->getResponse()->setBody($state);
    }

    public function saveAction() {
        $data = null;
        if ($this->getRequest()->getPost()) {
            try {
                $data = $this->getRequest()->getPost();

                if ($this->getRequest()->getParam("duplicate") && isset($data['pk'])) {
                    unset($data['pk']);
                    $this->_getSession()->addSuccess($this->__('Rate duplicated successfully'));
                    $this->_getShippingRatesAdminHelper()->setShippingRateSessionData($data);
                    $this->_redirect("*/*/edit");
                    return;
                }                
                
                $binds = array(
                    'website_id' => $data['website_id'],
                    'dest_country_id' => $data['dest_country_id'],
                    'dest_region_id' => $data['dest_region_id'],
                    'dest_zip' => $data['dest_zip'],
                    'condition_value' => $data['condition_value'],
                    'price' => $data['price'],
                    'condition_name' => $this->_getShippingRatesAdminHelper()->getWebsiteConfigData('carriers/tablerate/condition_name', $data['website_id'])
                );


                $resource = Mage::getResourceModel('shipping/carrier_tablerate');
                $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
                ;


                if (isset($data['pk'])) {
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
                } else {

                    // Add New
                    $query = "insert into {$resource->getMainTable()} (website_id, dest_country_id, dest_region_id, dest_zip, condition_value, condition_name, price) "
                            . "values (:website_id, :dest_country_id, :dest_region_id, :dest_zip, :condition_value, :condition_name, :price)";


                    $adapter->query($query, $binds);
                }
                
                $this->_getShippingRatesAdminHelper()->clearShippingRateSessionData();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Shipping Rate has been saved.'));
                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('pk')));
                } else {
                    $this->_redirect('*/*/', array('website' => $data['website_id']));
                }

                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                if (strpos($e->getMessage(), "SQLSTATE[23000]") !== false) {
                    $message = $this->__("The rate could not be saved because it duplicates another rate. Try changing some of the values and save again.");
                } else  {
                    $message = $this->__('An error occurred while saving this Shipping Rate.' . $e->getMessage());
                }
                Mage::getSingleton('adminhtml/session')->addError($message);
            }
            $this->_getShippingRatesAdminHelper()->setShippingRateSessionData($data);
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

        // Update the current website for when you return to the grid.
        Mage::getSingleton('admin/session')->setShippingRatesAdminSiteId($website);

        $conditionLabel = $label = Mage::getSingleton('shipping/carrier_tablerate')
                        ->getCode('condition_name_short', $this->_getShippingRatesAdminHelper()->getWebsiteConfigData('carriers/tablerate/condition_name', $website))
                . " <span class=\"required\">*</span>";
        $data = array('conditionLabel' => $conditionLabel);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($data));
    }

    public function massDeleteAction() {

        $ids = (array) $this->getRequest()->getParam('pk');
        try {
            $resource = Mage::getResourceModel('shipping/carrier_tablerate'); /* @var $resource Mage_Shipping_Model_Resource_Carrier_Tablerate */
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_write'); /* @var $adapter Zend_Db_Adapter_Abstract */
            $numRowsAffected = $adapter->delete($resource->getMainTable(), " `pk` in (" . implode(", ", $ids) . ")");
            $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', $numRowsAffected)
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__("An error occurred while updating records: '%s'", $e->getMessage()));
        }
        $this->_redirect('*/*/');
    }    
    
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        $success = false;
        try {
            if (!$id) {
                Mage::throwException($this->__("Please specify a rate to delete."));
            }
            
            $resource = Mage::getResourceModel('shipping/carrier_tablerate'); /* @var $resource Mage_Shipping_Model_Resource_Carrier_Tablerate */
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_write'); /* @var $adapter Zend_Db_Adapter_Abstract */
            $numRowsAffected = $adapter->delete($resource->getMainTable(), $adapter->quoteInto(" `pk` = ? ", $id));
            if ($numRowsAffected == 0) {
                Mage::throwException($this->__("The rate could not be deleted because it no longer exists. It was probably already deleted."));
            }
            $success = true;
        } catch (Mage_Core_Exception $ex ) {
            $this->_getSession()->addError($ex->getMessage());
        } catch (Exception $ex) {
            $this->_getSession()->addError($this->__("An problem occurred attempting to delete the tablerate: '%s'.", $ex->getMessage()));
        }
        if ($success) {
            $this->_getSession()->addSuccess($this->__("The rate was successfully deleted."));
        }
        
        $this->_redirect("*/*/index");
    }
    
   public function importAction() {
        $this->_initAction();
        $this->_title($this->__('Import Rates'));
        $this->renderLayout();       
   }
   
   public function importratesAction() {
        $websiteId = $this->getRequest()->getParam('website_id');
        $csvFile = !empty($_FILES['import']['tmp_name']) ? $_FILES['import']['tmp_name'] : null;
        
        
        if (!$websiteId || !$csvFile) {
            $this->_getSession()->addError($this->__("Please specify the website and file you wish to import"));
            $this->_redirect('*/*/import');
            return;
        }
        $_FILES = array('groups' => array('tmp_name' => array('tablerate' => array('fields' => array('import' => array('value' => $csvFile))))));
   
        $params = new Varien_Object();
        $params->setScopeId($websiteId);   
        $condition = array('groups' => array('tablerate' => array('fields' => array('condition_name' => array('value' => $this->_getShippingRatesAdminHelper()->getWebsiteConfigData('carriers/tablerate/condition_name', $websiteId))))));
        $params->addData($condition);
        $tableRate = Mage::getResourceModel('shipping/carrier_tablerate'); /* @var $tableRate Mage_Shipping_Model_Resource_Carrier_Tablerate */
        
        $message = "";
        try {
            $tableRate->uploadAndImport($params);
        } catch (Mage_Core_Exception $e) {
            $message = $e->getMessage();
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__("An error occurred whilst importing the tablerates: %s", $e->getMessage()));
            $this->_redirect('*/*/import');
            return;
        }
        if (!$message) {
            $message = $this->__("Table rates imported successfully");
            $this->_getSession()->addSuccess($message);
        } else {
            $this->_getSession()->addError(str_replace("\n", "<br />", $message));
        }
        $this->_redirect('*/*/index');       
   }
    

    /**
     * 
     * @return MageHack_ShippingRatesAdmin_Helper_Data
     */
    protected function _getShippingRatesAdminHelper() {
        return Mage::helper('shippingratesadmin');
    }

}
