<?php 
class Magestore_Inventory_Block_Adminhtml_Stocktransfering_Edit_Tab_Renderer_Fieldchanged
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $transferstockHistoryId = $row->getTransferStockHistoryId();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $sql = 'SELECT distinct(`field_name`) from ' . $resource->getTableName("erp_inventory_transfer_stock_history_content") . ' WHERE (transfer_stock_history_id = '.$transferstockHistoryId.')';
        $results = $readConnection->fetchAll($sql);
        $content = '';
        foreach ($results as $result) {
            $content .= $result['field_name'].'<br />';
        }
        return $content;
    }
}