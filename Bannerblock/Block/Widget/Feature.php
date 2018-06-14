<?php
namespace Ipragmatech\Bannerblock\Block\Widget;
use Ipragmatech\Bannerblock\Helper\Data;
use Magento\Catalog\Helper\Category;

class Feature extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_helper;
	protected $categoryFlatConfig;
	protected $_categoryFactory;
	/**
	 * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
	 */
	protected $_productCollectionFactory;
	/**
	 * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
	 */
	protected $_bestSellercollectionFactory;
	protected $_coreRegistry = null;
	//protected $_imageHelper;
	
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
			\Magento\Catalog\Model\CategoryFactory $categoryFactory,
			\Ipragmatech\Bannerblock\Helper\Data $helper,
			\Magento\Framework\Registry $registry,
			\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
			\Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellercollectionFactory,
			array $data = []
			) {
				//$this->_imageHelper = $context->getImageHelper();
				$this->_productCollectionFactory = $productCollectionFactory;
				$this->_bestSellercollectionFactory = $bestSellercollectionFactory;
				$this->_coreRegistry = $registry;
				$this->_helper = $helper;
				$this->categoryFlatConfig = $categoryFlatState;
				$this->_categoryFactory = $categoryFactory;
				$this->setTemplate('widget/feature.phtml');
				
				parent::__construct($context, $data);
	}
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}
	public function getFeatureProducts(){
		$featureProductLimit =$this->_helper->getConfig('bannerblocksection/topchoice/feature_product_qty');
		$collection = $this->_productCollectionFactory->create();
		$collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('is_feature','1');
		$collection->getSelect()->limit($featureProductLimit);
		
		return $collection;
	}
	
	public function getBestProduct(){
		/*$bestProductLimit =$this->_helper->getConfig('bannerblocksection/topchoice/bestseller_product_qty');
		$collection = $this->_bestSellercollectionFactory->create()->setModel(
				'Magento\Catalog\Model\Product'
			);
		$collection->getSelect();//->limit($bestProductLimit);
                $data = $collection->getData();
		return $data;*/
	 
		$data = [];
        $bestProductLimit = $this->_helper
            ->getConfig('bannerblocksection/topchoice/bestseller_product_qty');
        $collection = $this->_bestSellercollectionFactory
            ->create()->setModel('Magento\Catalog\Model\Product');
       
        
        $collection->getSelect()->limit($bestProductLimit);
	 	if(count($collection));
			$data = $collection->getData();

		return $data;
	}
	
}
