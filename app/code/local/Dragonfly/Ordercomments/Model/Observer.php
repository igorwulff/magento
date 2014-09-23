<?php
class Dragonfly_Ordercomments_Model_Observer extends Varien_Object 
{
	
	/**
	 * Add a customer order comment when the order is placed
	 * @param object $event
	 * @return
	 */
	public function saveOrder(Varien_Event_Observer $observer) {
		$_order = $observer->getEvent()->getOrder();
		$_request = Mage::app()->getRequest();
		$_comments = strip_tags($_request->getParam('orderComment'));

		if (!empty($_comments)) {
			$_comments = 'Klant opmerkingen: ' . $_comments;
			$_order->setCustomerNote($_comments);
		}
		
		return $this;
	}
	

}
