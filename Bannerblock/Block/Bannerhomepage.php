<?php
/**
 * Copyright © 2015 Ipragmatech . All rights reserved.
 */
namespace Ipragmatech\Bannerblock\Block;
use Ipragmatech\Bannerblock\Helper\Data;
use Magento\Catalog\Helper\Category;

class Bannerhomepage extends \Magento\Framework\View\Element\Template
{
	protected $_categoryFactory;
	protected $_helper;
	protected $categoryFlatConfig;
	protected $_sliderFactory;
	
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Catalog\Model\CategoryFactory $categoryFactory,
			\Ipragmatech\Bannerblock\Helper\Data $helper,
			\Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
			\Ipragmatech\Bannerblock\Model\SliderFactory $sliderFactory,
			array $data = []
			) {
				parent::__construct ( $context, $data );
				$this->_objectManager = $objectManager;
				$this->_categoryFactory = $categoryFactory;
				$this->_helper = $helper;
				$this->categoryFlatConfig = $categoryFlatState;
				$this->_sliderFactory = $sliderFactory;
	}
	
	public function getCategorymodel($id)
	{
		$_category = $this->_categoryFactory->create();
		$_category->load($id);
		return $_category;
	}
	/**
	 * Retrieve child store categories
	 *
	 */
	public function getChildCategories($category)
	{
		if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
			$subcategories = (array)$category->getChildrenNodes();
		} else {
			$subcategories = $category->getChildren();
		}
		$subCategoryids = (explode(',',$subcategories));
		return $subCategoryids;
	}
	
	public function getSliderImages(){
		$group = $this->_helper->getConfig('bannerblocksection/slider/slidergroup');
		$collection = $this->_sliderFactory->create()->getCollection();
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
	public function getFeatureProduct(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$featureClass = $objectManager->create('Ipragmatech\Bannerblock\Block\Widget\Feature');
		$imagehelper = $objectManager->create('Magento\Catalog\Helper\Image');
		$featureProducts = $featureClass->getFeatureProducts();
		return $featureProducts;
		
	}
	public function getBestseller(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$featureClass = $objectManager->create('Ipragmatech\Bannerblock\Block\Widget\Feature');
		$bestSellerProducts = $featureClass->getBestProduct();
		return $bestSellerProducts;
	}
	public function getNewProducts(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectManager->create('Ipragmatech\Bannerblock\Helper\Data');
		$newProductLimit = $helper->getConfig('bannerblocksection/newproduct/new_product_qty');
		$productFactory = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$collection = $productFactory->create();
		$todayDate  = date('Y-m-d', time());
		$date = $objectManager->create('\Magento\Framework\Stdlib\DateTime');
		$todayStartOfDayDate = date('Y-m-d H:i:s', time(0, 0, 0));//$date->setTime(0, 0, 0)->format('Y-m-d H:i:s');
		$todayEndOfDayDate = date('Y-m-d H:i:s', time(23, 59, 59));//$date->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		$collection->addAttributeToSelect('*');
		$collection->addAttributeToFilter(
				'news_from_date',
				[
						'or' => [
								0 => ['date' => true, 'to' => $todayEndOfDayDate],
								1 => ['is' => new \Zend_Db_Expr('null')],
						]
				],
				'left'
				)->addAttributeToFilter(
						'news_to_date',
						[
								'or' => [
										0 => ['date' => true, 'from' => $todayStartOfDayDate],
										1 => ['is' => new \Zend_Db_Expr('null')],
								]
						],
						'left'
				)->addAttributeToFilter(
								[
										['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
										['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
								]
				)->addAttributeToSort(
										'news_from_date',
										'desc'
			    )->getSelect()->limit($newProductLimit);
											
		return $collection;
	}
	public function getCategoryList(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$popularmenuClass = $objectManager->create('Ipragmatech\Bannerblock\Block\Widget\Popularmenu');
		$categoryIds = $popularmenuClass->getCategoryList();
		return $categoryIds;
	}
	public function getPopularmenuTitle(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$popularmenuClass = $objectManager->create('Ipragmatech\Bannerblock\Block\Widget\Popularmenu');
		$title = $popularmenuClass->getTitle();
		return $title;
	}
}