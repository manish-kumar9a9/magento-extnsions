<?php

namespace Ipragmatech\Bannerblock\Model\Config;

class SliderGroups implements \Magento\Framework\Option\ArrayInterface
{

	 /**
     * @var \Ipragmatech\Slider\Model\sliderFactory
     */
    protected $_sliderFactory;
    
     /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ipragmatech\Slider\Model\sliderFactory $sliderFactory
     * @param \Ipragmatech\Slider\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Ipragmatech\Bannerblock\Model\SliderFactory $sliderFactory
    ) {
        $this->_sliderFactory = $sliderFactory;
    }
    
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	$optionArray = array();
    	foreach($this->toArray() as $arr){
    		$optionArray[] = array( 'value' => $arr , 'label' => $arr);
    	}
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
    	$group = array();
    	$collection = $this->_sliderFactory->create()->getCollection();
    	
    	foreach($collection as $slider){
    		$group[$slider->getGroup()]  = $slider->getGroup();
    	}
        return $group;
    }
}