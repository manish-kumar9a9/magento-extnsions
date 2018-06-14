<?php
namespace Ipragmatech\Bannerblock\Block\Widget;
use Ipragmatech\Bannerblock\Helper\Data;
use Magento\Catalog\Helper\Category;


class Popularmenu extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_productCollectionFactory;
	protected $_helper;
	protected $categoryFlatConfig;
	protected $_categoryFactory;
	
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
			\Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
			\Magento\Catalog\Model\CategoryFactory $categoryFactory,
			\Ipragmatech\Bannerblock\Helper\Data $helper,
			array $data = []
			)
	{
		$this->setTemplate('widget/popularmenu.phtml');
		$this->_helper = $helper;
		$this->categoryFlatConfig = $categoryFlatState;
		$this->_categoryFactory = $categoryFactory;
		$this->_productCollectionFactory = $productCollectionFactory;
		parent::__construct($context, $data);
	}
	public function getCategoryList(){
		$selectedCategoriesId = $this->_helper->getConfig('bannerblocksection/popularmenu/setting');
		$categoryids = (explode(',',$selectedCategoriesId));
		return $categoryids;
	}
	
	public function getCategory($id){
		$category = $this->_categoryFactory->create();
		if($id){
			$category->load($id);
		}
		return $category;
	}
	public function getCategorymodel($id)
	{
		$_category = $this->_categoryFactory->create();
		$_category->load($id);
		return $_category;
	}
	public function getTitle(){
		return $title = $this->_helper->getConfig('bannerblocksection/popularmenu/title');
	}

}

