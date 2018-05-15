<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Inventorydropship
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorydropship Helper
 * 
 * @category    Magestore
 * @package     Magestore_Inventorydropship
 * @author      Magestore Developer
 */
class Magestore_Inventorydropship_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    const XML_PATH_FORGOT_EMAIL_IDENTITY = 'customer/password/forgot_email_identity';
    
    public function getDropshipStatus()
    {
        return array(
            1    => Mage::helper('inventorydropship')->__('Awaiting supplier\'s confirmation'),
            2    => Mage::helper('inventorydropship')->__('Awaiting approval'),
            3    => Mage::helper('inventorydropship')->__('Awaiting supplier\'s shipment'),
            4    => Mage::helper('inventorydropship')->__('Partially shipped'),
            5    => Mage::helper('inventorydropship')->__('Canceled'),
            6    => Mage::helper('inventorydropship')->__('Complete'),
        );
    }
    
    public function getDropshipForSupplier()
    {
        return array(
            1    => Mage::helper('inventorydropship')->__('Awaiting confirmation'),
            2    => Mage::helper('inventorydropship')->__('Awaiting admin\'s approval'),
            3    => Mage::helper('inventorydropship')->__('Awaiting shipment'),
            4    => Mage::helper('inventorydropship')->__('Partially shipped'),
            5    => Mage::helper('inventorydropship')->__('Canceled'),
            6    => Mage::helper('inventorydropship')->__('Complete'),
        );
    }
    
    public function sendEmailOfferToSupplier($dropshipId)
    {
        if(!Mage::getStoreConfig('inventory/dropship/sendemail'))
            return;
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $supplierId = $dropship->getSupplierId();
        $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
        $templateId = Mage::getStoreConfig('inventory/dropship/email_request_to_supplier');
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $admin = Mage::getModel('admin/session')->getUser();  
        $from_email = $admin->getEmail(); //fetch sender email Admin
        $from_name = $admin->getName(); //fetch sender name Admin
        $sender = array('email' => $from_email, 'name' => $from_name);        
        $receipientEmail = $supplier->getEmail(); 
        $receipientName = $supplier->getContactName().'('.$supplier->getName().')';
        $mailTemplate
            ->setTemplateSubject('Email Request Product To Supplier')
            ->sendTransactional(
                $templateId, $sender, $receipientEmail, $receipientName, array(
                    'dropship' => $dropship,
                    'receipient_name' => $receipientName,                
                )
        );
        $translate->setTranslateInline(true);
    }
    
    public function sendEmailCancelDropShipToSupplier($dropshipId)
    {
        if(!Mage::getStoreConfig('inventory/dropship/sendemail'))
            return;
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $supplierId = $dropship->getSupplierId();
        $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
        $templateId = Mage::getStoreConfig('inventory/dropship/email_cancel_to_customer');
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $admin = Mage::getModel('admin/session')->getUser();  
        $from_email = $admin->getEmail(); //fetch sender email Admin
        $from_name = $admin->getName(); //fetch sender name Admin
        $sender = array('email' => $from_email, 'name' => $from_name);        
        $receipientEmail = $supplier->getEmail(); 
        $receipientName = $supplier->getContactName().'('.$supplier->getName().')';
        $mailTemplate
            ->setTemplateSubject('Cancel Drop Ship')
            ->sendTransactional(
                $templateId, $sender, $receipientEmail, $receipientName, array(
                    'dropship' => $dropship,
                    'receipient_name' => $receipientName,                
                )
        );
        $translate->setTranslateInline(true);
    }
    
    public function sendEmailCancelDropShipToAdmin($dropshipId)
    {        
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $supplierId = $dropship->getSupplierId();
        $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
        $templateId = Mage::getStoreConfig('inventory/dropship/email_cancel_to_admin');
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $admin = Mage::getModel('admin/session')->getUser();  
        $from_email = $supplier->getEmail();
        $from_name = $supplier->getContactName().'('.$supplier->getName().')';
        $sender = array('email' => $from_email, 'name' => $from_name);        
        $receipientEmail = $dropship->getAdminEmail(); 
        $receipientName = $dropship->getAdminName();
        $mailTemplate
            ->setTemplateSubject('Cancel Drop Ship')
            ->sendTransactional(
                $templateId, $sender, $receipientEmail, $receipientName, array(
                    'dropship' => $dropship,
                    'receipient_name' => $receipientName,                
                    'supplier' => $supplier,                
                )
        );
        $translate->setTranslateInline(true);
    }
    
    public function sendPasswordResetConfirmationEmail($supplier,$newPassword)
    {        
        try{
            $templateId = Mage::getStoreConfig('inventory/dropship/email_reset_password');
            $mailTemplate = Mage::getModel('core/email_template');
            $translate = Mage::getSingleton('core/translate');                
            $sender = Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY);
            $receipientEmail = $supplier->getEmail(); 
            $receipientName = $supplier->getContactName().'('.$supplier->getName().')';
            $mailTemplate
                ->setTemplateSubject('Reset Supplier Password')
                ->sendTransactional(
                    $templateId, $sender, $receipientEmail, $receipientName, array(
                        'supplier' => $supplier,
                        'password' => $newPassword,                
                    )
            );
            $translate->setTranslateInline(true);
        }catch(Exception $e){
            
        }
    }
    
    public function sendEmailConfirmDropShipToAdmin($dropshipId)
    {        
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $supplierId = $dropship->getSupplierId();
        $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
        $templateId = Mage::getStoreConfig('inventory/dropship/email_confirm_to_admin');
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $admin = Mage::getModel('admin/session')->getUser();  
        $from_email = $supplier->getEmail();
        $from_name = $supplier->getContactName().'('.$supplier->getName().')';
        $sender = array('email' => $from_email, 'name' => $from_name);        
        $receipientEmail = $dropship->getAdminEmail(); 
        $receipientName = $dropship->getAdminName();
        $mailTemplate
            ->setTemplateSubject('Confirm Drop Ship')
            ->sendTransactional(
                $templateId, $sender, $receipientEmail, $receipientName, array(
                    'dropship' => $dropship,
                    'receipient_name' => $receipientName,                
                    'supplier' => $supplier,                
                )
        );
        $translate->setTranslateInline(true);
    }
    
    public function sendEmailApproveDropShipToSupplier($dropshipId)
    {
        if(!Mage::getStoreConfig('inventory/dropship/sendemail'))
            return;
        
        $supplierNeedToConfirmProvide = false;
        if(Mage::getStoreConfig('inventory/dropship/supplier_confirm_provide'))
            $supplierNeedToConfirmProvide = true;        
        $adminNeedToApprove = false;
        if(Mage::getStoreConfig('inventory/dropship/admin_approve'))
            $adminNeedToApprove = true;
        $supplierNeedToConfirmShipped = false;        
        if(Mage::getStoreConfig('inventory/dropship/supplier_confirm_shipped'))
            $supplierNeedToConfirmShipped = true;            
        if($supplierNeedToConfirmProvide){
            if(!$adminNeedToApprove && !$supplierNeedToConfirmShipped){
                $supplierNeedToConfirmProvide = false;
            }
        }
            
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $supplierId = $dropship->getSupplierId();
        $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
        $templateId = Mage::getStoreConfig('inventory/dropship/email_approve_to_customer');
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $admin = Mage::getModel('admin/session')->getUser();  
        if($admin){
            $from_email = $admin->getEmail(); //fetch sender email Admin
            $from_name = $admin->getName(); //fetch sender name Admin
        }else{
            $from_email = $dropship->getAdminEmail(); 
            $from_name = $dropship->getAdminName();
        }
        $sender = array('email' => $from_email, 'name' => $from_name);        
        $receipientEmail = $supplier->getEmail(); 
        $receipientName = $supplier->getContactName().'('.$supplier->getName().')';
        $mailTemplate
            ->setTemplateSubject('Approve Drop Ship')
            ->sendTransactional(
                $templateId, $sender, $receipientEmail, $receipientName, array(
                    'dropship' => $dropship,
                    'receipient_name' => $receipientName,
                    'supplier_need_to_confirm_provide' => $supplierNeedToConfirmProvide,
                )
        );
        $translate->setTranslateInline(true);        
    }
    
    public function sendEmailConfirmShippedToAdmin($dropshipId,$shipped)
    {
        $dropship = Mage::getModel('inventorydropship/inventorydropship')->load($dropshipId);
        $supplierId = $dropship->getSupplierId();
        $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
        $templateId = Mage::getStoreConfig('inventory/dropship/email_confirm_shipped_to_admin');
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $admin = Mage::getModel('admin/session')->getUser();  
        $from_email = $supplier->getEmail();
        $from_name = $supplier->getContactName().'('.$supplier->getName().')';
        $sender = array('email' => $from_email, 'name' => $from_name);        
        $receipientEmail = $dropship->getAdminEmail(); 
        $receipientName = $dropship->getAdminName();
        $mailTemplate
            ->setTemplateSubject('Confirm Drop Ship')
            ->sendTransactional(
                $templateId, $sender, $receipientEmail, $receipientName, array(
                    'dropship' => $dropship,
                    'receipient_name' => $receipientName,                
                    'supplier' => $supplier,  
                    'items' => $shipped,
                )
        );
        $translate->setTranslateInline(true);
    }
    
    public function sendEmailRefundToSupplier($supplierReId,$items)
    {
        if(!Mage::getStoreConfig('inventory/dropship/sendemail'))
            return;
        try{            
            $supplierId = $supplierReId;
            $supplier = Mage::getModel('inventory/supplier')->load($supplierId);
            $templateId = Mage::getStoreConfig('inventory/dropship/email_refund_to_supplier');
            $mailTemplate = Mage::getModel('core/email_template');
            $translate = Mage::getSingleton('core/translate');
            $admin = Mage::getModel('admin/session')->getUser();  
            $from_email = $admin->getEmail(); //fetch sender email Admin
            $from_name = $admin->getName(); //fetch sender name Admin
            $sender = array('email' => $from_email, 'name' => $from_name);        
            $receipientEmail = $supplier->getEmail(); 
            $receipientName = $supplier->getContactName().'('.$supplier->getName().')';
            $mailTemplate
                ->setTemplateSubject('Email Refund Product To Supplier')
                ->sendTransactional(
                    $templateId, $sender, $receipientEmail, $receipientName, array(
                        'items' => $items,
                        'receipient_name' => $receipientName,                
                    )
            );
            $translate->setTranslateInline(true);
        }catch(Exception $e){
            
        }
    }
}