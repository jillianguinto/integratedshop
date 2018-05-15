<?php
 
class Unilab_Perapal_Model_Observer 
{
    public function changeOrderStatus(Varien_Event_Observer $observer)
    {

        
            $invoice = $observer->getEvent()->getInvoice();
            $order = $invoice->getOrder();
            $orderstate = $order->getState();
            $payment = $order->getPayment();
            $paymentmethod =  $payment->getMethodInstance()->getCode();

            //get payment method for condition kbanktransfer or kbankgateway
            if($paymentmethod == 'perapal'):
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                // $order->setState(Mage_Sales_Model_Order::STATE_PENDING, true);        
                $order->save();
            endif;
    }
 

}

?>