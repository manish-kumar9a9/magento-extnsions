<?php
namespace Ipragmatech\Bannerblock\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Slidergroup implements ArrayInterface
{
	protected $_sliderFactory;
	
	public function __construct(
			\Ipragmatech\Bannerblock\Model\SliderFactory $sliderFactory
			)
	{
		$this->_sliderFactory = $sliderFactory;
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
		$collection = $this->_sliderFactory->create()->getCollection();
		$collection->addFieldToSelect('group');
		$slideGroupList = array();
		foreach ($collection as $item){
			$slideGroupList[$item->getGroup()] = __($item->getGroup());
		}
		return $slideGroupList;
	}
}