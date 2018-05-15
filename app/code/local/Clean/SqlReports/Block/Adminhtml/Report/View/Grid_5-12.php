<?php

class Clean_SqlReports_Block_Adminhtml_Report_View_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_sqlQueryResults;

    public function __construct()
    {
        parent::__construct();
		
       
        $this->setId('reportsGrid');
        $this->setDefaultSort('report_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->unsetChild('search_button');
        $this->unsetChild('reset_filter_button');
		$this->setChild('store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', array('store'=>null)))
                ->setTemplate('report/store/switcher.phtml')
        );
        return $this;
    }

    /**
     * @return Clean_SqlReports_Model_Report
     */
    protected function _getReport()
    {
        return Mage::registry('current_report');
    }

    /**
     * @author Lee Saferite <lee.saferite@aoe.com>
     * @return Varien_Data_Collection_Db
     */
    protected function _createCollection()
    {
        $report = $this->_getReport();

        /** @var $connection Varien_Db_Adapter_Interface */
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $collection = new Varien_Data_Collection_Db($connection);
        
		
		
		$sql = explode(" GROUP BY ",$report->getData('sql_query'));
		$where_date = ($_GET["report_type"] == "created_at_order" ? "sales_flat_order.created_at" : "updated_at");
		$from_date = explode("/",$_GET["from"]);
		$to_date = explode("/",$_GET["to"]);
		switch($_GET["period_type"]) {
			case "day":
				$from_date = $_GET["from"];
				$to_date = $_GET["to"];
				$where_date = "DATE_FORMAT(".$where_date.",'%m/%d/%Y')";
				break;
			case "month":
				$from_date = $from_date[0].'/'.$from_date[2];
				$to_date = $to_date[0].'/'.$to_date[2];
				$where_date = "DATE_FORMAT(".$where_date.",'%m/%Y')";
				break;
			case "year":
				$from_date = $from_date[2];
				$to_date = $to_date[2];
				$where_date = "DATE_FORMAT(".$where_date.",'%Y')";
				break;
		}
		if($_GET["from"] != "" && $_GET["to"] != "") {
			$where_date = " ".$where_date." BETWEEN '".$from_date."' AND '".$to_date."' ";
			$where_array []= $where_date;
		}
		else 
			$where_date = "";
		if($_GET["show_order_statuses"] == "1") {
			$where_status = " status IN ('".implode("','",$_GET["order_statuses"])."') ";
			$where_array []= $where_status;
			}
		else
			$where_status = "";
		if($_GET["store_ids"] != "") {
			$where_store_id = " sales_flat_order.store_id IN (".$_GET["store_ids"].")";
			$where_array []= $where_store_id;
			}
		else
			$where_store_id = "";
		
		
	
		$where = implode(" AND ",$where_array);
		if($where != "")
			$where = " WHERE ".$where;
		$sql = $sql[0].$where." GROUP BY ".$sql[1];
		
		$collection->getSelect()->from(new Zend_Db_Expr("(" .$sql . ")"));
        return $collection;
    }

    protected function _prepareCollection()
    {
        if (isset($this->_collection)) {
            return $this->_collection;
        }

        $collection = $this->_createCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $collection = $this->_createCollection();
        $collection->setPageSize(1);
        $collection->load();

        $items = $collection->getItems();
        if (count($items)) {
            $item = reset($items);
            foreach ($item->getData() as $key => $val) {
                $this->addColumn(
                    $key,
                    array(
                        'header'   => Mage::helper('core')->__($key),
                        'index'    => $key,
                        'filter'   => false,
                        'sortable' => true,
                        'column_css_class' => (strpos($val,"₱ ") === false ? " " : "money"),
                    )
                );
            }
        }

        return parent::_prepareColumns();
    }
}