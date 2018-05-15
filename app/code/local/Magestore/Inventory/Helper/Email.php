<?php

class Magestore_Inventory_Helper_Email extends Mage_Core_Helper_Abstract {

    const XML_PATH_WAREHOUSE_EMAIL = 'inventory/email/warehouse_email';
    const XML_PATH_SYSTEM_EMAIL = 'inventory/email/system_email';
    const XML_PATH_SENDSTOCK_EMAIL = 'inventory/email/sendstock_email';

    public function sendSendstockEmail($warehouse,$stockId,$isSendstock,$stockName) {
        $user = Mage::getModel('admin/session')->getUser();
        $storeId = Mage::app()->getStore()->getId();
        $template = Mage::getStoreConfig(self::XML_PATH_SENDSTOCK_EMAIL, $storeId);
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $from_name = $user->getUsername();
        $from_email = $user->getEmail();
        $sender = array('email' => $from_email, 'name' => $from_name);
        $mailTemplate
            ->setTemplateSubject('Stock Notification')
            ->sendTransactional(
                $template, $sender, $warehouse->getManagerEmail(), $warehouse->getManagerName(), array(
                'requeststockid' => $stockId,
				'issendstock'	=> $isSendstock,
				'stockName' => $stockName
                ),$storeId
        );
        $translate->setTranslateInline(true);

    }

    public function sendWarehouseEmail($warehouse) {
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $str = 'qty%5Bto%5D=' . "$qty_notice";
        $filter = base64_encode($str);
        $link = Mage::helper('adminhtml')->getUrl("inventoryadmin/adminhtml_warehouse/edit", array('filter' => $filter, 'id' => $warehouse->getId(), 'loadptab' => true));
        $storeId = Mage::app()->getStore()->getId();
        $template = Mage::getStoreConfig(self::XML_PATH_WAREHOUSE_EMAIL, $storeId);
        $mailTemplate = Mage::getModel('core/email_template');

        $translate = Mage::getSingleton('core/translate');
        $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin
        $sender = array('email' => $from_email, 'name' => $from_name);
        $mailTemplate
            ->setTemplateSubject('Warehouse products are low')
            ->sendTransactional(
                $template, $sender, $warehouse->getManagerEmail(), $warehouse->getManagerName(), array(
                'warehouse_name' => $warehouse->getName(),
                'manager_name' => $warehouse->getManagerName(),
                'qty_notice' => $qty_notice,
                'link' => $link
                )
        );
        $translate->setTranslateInline(true);
    }

    public function sendSystemEmail() {
        $qty_notice = Mage::getStoreConfig('inventory/notice/qty_notice');
        $str = 'qty%5Bto%5D=' . "$qty_notice" . '&price%5Bcurrency%5D=USD';
        $filter = base64_encode($str);
        $link = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/index', array('product_filter' => $filter));
        $storeId = Mage::app()->getStore()->getId();
        $template = Mage::getStoreConfig(self::XML_PATH_SYSTEM_EMAIL, $storeId);
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
        $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin
        $sender = array('email' => $from_email, 'name' => $from_name);
        $to = Mage::getStoreConfig('inventory/notice/admin_email');
        $receipientName = 'Managers';
        $mailTemplate
            ->setTemplateSubject('Products of system are low')
            ->sendTransactional(
                $template, $sender, $to, $receipientName, array(
                'manager_name' => $receipientName,
                'qty_notice' => $qty_notice,
                'link' => $link
                )
        );
        $translate->setTranslateInline(true);
    }

}

?>
