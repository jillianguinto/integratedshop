<?php 
class Magestore_Inventory_Block_Adminhtml_Supplyneeds_Renderer_Qtyforsupply
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) 
    {
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('top_filter'));
        $product_id = $row->getProductId();
        $warehouse = $requestData['warehouse_select'];
        $datefrom = $requestData['date_from'];
        if(!$datefrom){
            $now = now();
            // $datefrom = date("Y-m-d",strtotime($now));
            $datefrom = date("Y-m-d",Mage::getModel('core/date')->timestamp($now));
        }
        $dateto = $requestData['date_to'];
        $method = Mage::getStoreConfig('inventory/calculate/supplyneeds_method');
        $min_needs = Mage::helper('inventory/supplyneeds')->calMin($product_id,$warehouse);
        if($datefrom && $dateto && $method==2 && (strtotime($datefrom) <=  strtotime($dateto))){
            $max_needs = ceil($row->getTotalOrder() / 10) + $min_needs;
        }elseif($datefrom && $dateto && $method==1 &&  strtotime($datefrom) <=  strtotime($dateto)){
            $max_needs = Mage::helper('inventory/supplyneeds')->calMaxExponential($product_id, $datefrom, $dateto, $warehouse);
        }else{
			$min_needs = 0;
            $max_needs = 0;
        }
        if(!$dateto ||  strtotime($datefrom) >  strtotime($dateto))
        {
            $min_needs = 0;
            $max_needs = 0;
        }
        if($min_needs<0){
            $min_needs = 0;
        }
        if($max_needs<0){
			$min_needs = 0;
            $max_needs = 0;
        }
        $url = $this->getUrl('inventoryadmin/adminhtml_supplyneeds/chart');//.'product_id/'.$product_id;
        return '<p style="text-align:center">Min: <label name="minNeeds" id="min_need_'.$product_id.'">'.$min_needs.'</label> &nbsp; - &nbsp; Max: <label name="maxNeeds" id="max_need_'.$product_id.'">'.$max_needs.'</label></p>
                <p style="text-align:center"><a name="url" href="javascript:void(0)" onclick="drawChart('.$product_id.')">Sales History</a></p>
                <script type="text/javascript">
                    function drawChart(product_id){
                        var url = "'.$url.'product_id/"+product_id;
                        TINY.box.show(url,1, 800, 400, 1);
                    }
                </script>
                ';   
    }
}