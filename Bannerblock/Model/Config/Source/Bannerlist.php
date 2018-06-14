<?php 
namespace Ipragmatech\Bannerblock\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

class Bannerlist implements ArrayInterface
{
	protected $_categoryHelper;
	protected $categoryFlatConfig;
	
	public function __construct(
		\Magento\Catalog\Helper\Category $catalogCategory,
		\Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
	)
	{
		$this->_categoryHelper = $catalogCategory;
		$this->categoryFlatConfig = $categoryFlatState;
	}
	
	/*
	 * Return categories helper
	 */
	
	public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
	{
		return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
	}
	
	/*  
	 * Option getter
	 * @return array
	 */
	public function toOptionArray()
	{
		$arr = $this->toArray();
		$ret = [];
		foreach ($arr as $key => $value)
		{
			$ret[] = [
				'value' => $key,
				'label' => $value
			];
		}
		return $ret;
	}
	
	/*
	 * Get options in "key-value" format
	 * @return array
	 */
	public function toArray()
	{
		$categories = $this->getStoreCategories(true,false,true);
		//$this->_objectManager->create('Magento\Catalog\Helper\Category')->getStoreCategories(true,false,true);
		$catagoryList = array();
		foreach ($categories as $category){
			$catagoryList[$category->getEntityId()] = __($category->getName());
			$subcategories = $this->getChildCategories($category);
			if($subcategories){
				foreach ($subcategories as $sc){
					$ssc = $this->getChildCategories($sc);
// 					foreach ($ssc as $s){}
				}
			}
		}
		return $catagoryList;
	}
	
	//sub category
	public function getChildCategories($category)
	{
		if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
			$subcategories = (array)$category->getChildrenNodes();
		} else {
			$subcategories = $category->getChildren();
		}
		return $subcategories;
	}
}