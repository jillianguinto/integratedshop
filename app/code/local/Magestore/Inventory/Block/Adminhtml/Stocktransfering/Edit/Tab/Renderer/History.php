<?php 
class Magestore_Inventory_Block_Adminhtml_Stocktransfering_Edit_Tab_Renderer_History
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $transferstockHistoryId = $row->getTransferStockHistoryId();
        $url = $this->getUrl('inventoryadmin/adminhtml_stocktransfering/showhistory');
        return '<p style="text-align:center"><a name="url" href="javascript:void(0)" onclick="showhistory('.$transferstockHistoryId.')">'.Mage::helper('inventory')->__('View').'</a></p>
                <script type="text/javascript">
                    function showhistory(transferstockHistoryId){
                        var transferstockHistoryId  = transferstockHistoryId ;
                        var url = "'.$url.'transferstockHistoryId/"+transferstockHistoryId ;
                        TINY.box.show(url,1, 800, 400, 1);
                    }
                </script>
                ';   
    }
}