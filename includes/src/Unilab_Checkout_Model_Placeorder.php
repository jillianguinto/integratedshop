<?php

class Unilab_Checkout_Model_Placeorder
{
		
	public function hookToSaveOrderAfterEvent(Varien_Event_Observer $observer)
	{
				
		file_put_contents('./debug.txt', 'hookToSaveOrderAfterEvent'.PHP_EOL,FILE_APPEND);
		
	}
	
	
}