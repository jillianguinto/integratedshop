<?php

class Unilab_CityDropdown_Adminhtml_CitydropdownController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('citydropdown/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('City Dropdown Manager'), Mage::helper('adminhtml')->__('City Dropdown Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }    

    public function newAction() {
        $this->_forward('edit');
    } 
	
	public function editAction() {
        $id     = $this->getRequest()->getParam('city_id');
        $model  = Mage::getModel('citydropdown/citydropdown')->load($id);

        if ($model->getCityId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
				
            Mage::register('citydropdown_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('citydropdown/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('City Manager'), Mage::helper('adminhtml')->__('City Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('City News'), Mage::helper('adminhtml')->__('City News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('citydropdown/adminhtml_citydropdown_edit'))
                ->_addLeft($this->getLayout()->createBlock('citydropdown/adminhtml_citydropdown_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('citydropdown')->__('City does not exist'));
            $this->_redirect('*/*/');
        }
    } 
	
	 public function saveAction() {
        if ($data = $this->getRequest()->getPost()) { 

            $model = Mage::getModel('citydropdown/citydropdown');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('city_id'));

            try { 
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('citydropdown')->__('City was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('city_id' => $model->getCityId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('city_id' => $this->getRequest()->getParam('city_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('citydropdown')->__('Unable to find City to save'));
        $this->_redirect('*/*/');
    }
	public function deleteAction() {
        if( $this->getRequest()->getParam('city_id') > 0 ) {
            try {
                $model = Mage::getModel('citydropdown/citydropdown');

                $model->setId($this->getRequest()->getParam('city_id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('City was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('city_id' => $this->getRequest()->getParam('city_id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $homepagebannerIds = $this->getRequest()->getParam('citydropdown');
        if(!is_array($homepagebannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($homepagebannerIds as $homepagebannerId) {
                    $citydropdown = Mage::getModel('citydropdown/citydropdown')->load($homepagebannerId);
                    $citydropdown->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($homepagebannerIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	 
}
