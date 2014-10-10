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
    
	//Configurable product
    public function recalculateFinalPrice(Varien_Event_Observer $observer)
    {
    	if(!is_null($observer)){
    		$quoteItem = $observer->getEvent()->getQuoteItem();
			$product = $observer->getEvent()->getProduct();
				
	
			if($product->getTypeId() == "configurable"){
				$data = array();
				
				$quote = $quoteItem->getQuote();
				foreach($product->getCustomOptions() as $option){
					$data[] = unserialize($option->getValue());
				}
				
				if(isset($data[0]['super_attribute'][134], $data[0]['super_attribute'][92])){
					$size = (int)$data[0]['super_attribute'][134];
					$color = (int)$data[0]['super_attribute'][92];
	
				    $collection = Mage::getModel('catalog/product_type_configurable')
						->getUsedProductCollection($product)
	                    ->addAttributeToSelect('color')
						->addAttributeToSelect('size')
	                	->addFilterByRequiredOptions();
						
					$currentProduct = null;
				    foreach($collection as $value){
				    	if($value->getColor() == $color && $value->getSize() == $size){
				    		$currentProduct = $value->load();
							break;
				    	}					
				    }
					
					if(!is_null($currentProduct)){
						$product->setPrice($currentProduct->getPrice());
						$product->setFinalPrice($currentProduct->getFinalPrice());
					}
				}
			}
		}
    }
}
