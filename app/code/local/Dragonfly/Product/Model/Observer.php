<?php
class Dragonfly_Product_Model_Observer
{
	protected static $isProductNew = false;
	
	public function addBeforeProduct(Varien_Event_Observer $observer){
		self::$isProductNew = $observer->getEvent()->getProduct()->isObjectNew();
		return true;
	}
	
    /**
     * Adds default options after product is saved
     *
     * @param Varien_Event_Observer $observer observer object
     *
     * @return boolean
     */
    public function addDefaultProductOptions(Varien_Event_Observer $observer)
    {
	    $product = $observer->getEvent()->getProduct();
		
		if(self::$isProductNew){
			$product->setData('has_options', 1);
			
			$optionBlabla = array(
				'title' => 'Blabla',
				'type' => 'checkbox',
			    'is_require' => 0,
			    'sort_order' => 0,
			    'values' => array(
			    	array(
					    'title' => 'Blabla',
					    'price' => 2.50,
					    'price_type' => 'fixed',
					    'sku' => '',
						'sort_order' => 0
					)
				)
			);
			
			$optionDefault = array(
				'title' => 'Blabla',
				'type' => 'field',
			    'is_require' => 0,
			    'sort_order' => 0,
			    'max_characters' => 0,
			    'price' => 10.0,
			    'price_type' => 'fixed',
			    'sku' => '',
			);
			
			$optionInstance = Mage::getSingleton('catalog/product_option')->unsetOptions();
			foreach($product->getCategoryCollection() as $category){
				if(in_array($category->getId(), array(79, 93)) !== false){
					$optionInstance->addOption($optionBlabla);
					break;
				}
			}
			$optionInstance->addOption($optionDefault);
			$optionInstance->setProduct($product);
			$optionInstance->saveOptions();
			$product->setCanSaveCustomOptions(true);
			
			// $product->save() gives duplicate entry error.
			$product->getResource()->save($product);
		}
		
	    return true;
    }
}