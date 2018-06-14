<?php
namespace Ipragmatech\Bannerblock\Block\Widget;

class SliderSlider extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	 /**
     * @var \Ipragmatech\Slider\Model\sliderFactory
     */
    protected $_sliderFactory;

    protected $_template = 'widget/sliderslider.phtml';
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
         \Ipragmatech\Bannerblock\Model\SliderFactory $sliderFactory
    ) {
    	 $this->_sliderFactory = $sliderFactory;
        parent::__construct($context);
    }
    
    public function getSliderImages(){
    	$collection = $this->_sliderFactory->create()->getCollection();
    	$group = $this->getData('group');
    	$collection->addFieldToFilter('group' ,$group );
    	$collection->addFieldToFilter('is_active' ,1 );
    	return $collection;
    }
    public function getImageMediaPath(){
    	return $this->getUrl('pub/media',['_secure' => $this->getRequest()->isSecure()]);
    }
    
    public function getMobileSlider(){
    	$collection = $this->_sliderFactory->create()->getCollection();
    	$collection->addFieldToFilter('is_active' ,1 );
    	$collection->addFieldToFilter('in_mobile' ,1 );
    	return $collection;
    }
}