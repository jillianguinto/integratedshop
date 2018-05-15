<?php
class Unilab_Fee_Model_Sales_Order_Total_Creditmemo_Fee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
/*   		$order = $creditmemo->getOrder();
		$feeAmountLeft = $order->getFeeAmountInvoiced() - $order->getFeeAmountRefunded();
		$basefeeAmountLeft = $order->getBaseFeeAmountInvoiced() - $order->getBaseFeeAmountRefunded();
		if ($basefeeAmountLeft > 0) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $feeAmountLeft);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $basefeeAmountLeft);
			$creditmemo->setFeeAmount($feeAmountLeft);
			$creditmemo->setBaseFeeAmount($basefeeAmountLeft);
		}
		return $this; */
		 
		
		$order 			   = $creditmemo->getOrder();
        $allowedAmount     = $order->getFeeAmount()-$order->getFeeAmountRefunded();
        $baseAllowedAmount = $order->getBaseFeeAmount()-$order->getBaseFeeAmountRefunded();

        $fee               = $order->getFeeAmount();
        $baseFee           = $order->getBaseFeeAmount();   
		
        if ($creditmemo->hasBaseFeeAmount()) {
           $baseFeeAmount = Mage::app()->getStore()->roundPrice($creditmemo->getBaseFeeAmount());  
            if ($baseFeeAmount < Mage::app()->getStore()->roundPrice($baseAllowedAmount) + 0.0001) { 
                if ($baseFeeAmount > $baseAllowedAmount - 0.0001) {
                    $fee     = $allowedAmount;
                    $baseFee = $baseAllowedAmount;
                } else {
                    if ($baseFee != 0) {
                        $fee = $fee * $baseFeeAmount / $baseFee;
                    }
                    $fee     = Mage::app()->getStore()->roundPrice($fee);
                    $baseFee 	  = $baseFeeAmount;
                }
            } else {
                $baseAllowedAmount = $order->getBaseCurrency()->format($baseAllowedAmount,null,false);
                Mage::throwException(
                    Mage::helper('insurance')->__('Maximum Insurance amount allowed to refund is: %s', $baseAllowedAmount)
                );
            }
        } else { 
            $fee           = $allowedAmount;
            $baseFee       = $baseAllowedAmount;
        } 
		
        $creditmemo->setFeeAmount($fee);
        $creditmemo->setBaseFeeAmount($baseFee); 

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$fee);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$baseFee); 
        return $this; 
	}
}
