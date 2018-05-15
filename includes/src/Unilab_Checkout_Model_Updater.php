<?php
class Unilab_Checkout_Model_Updater {
	
	public function updateItemCart($e) {
		$_this = $e->cart;
		$data = $e->info;
		
		//file_put_contents('./debug.txt', 'here'.PHP_EOL,FILE_APPEND);
		
		foreach ($data as $itemId => $itemInfo) {
			$item = $_this -> getQuote() -> getItemById($itemId);
			if (!$item)
				continue;
			if (!isset($itemInfo['option']) or empty($itemInfo['option']))
				continue;
			foreach ($item->getOptions() as $option) {
				if ($option -> getCode() == 'info_buyRequest') {
					$unserialized = unserialize($option -> getValue());
					$unserialized['super_attribute'] = $itemInfo['option'];
					$option -> setValue(serialize($itemInfo['option']));
				} elseif ($option -> getCode() == 'attributes') {
					$option -> setValue(serialize($itemInfo['option']));
				}
			}
			$item -> save();
		}
	}

}
?>