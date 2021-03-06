<?php
namespace Ipragmatech\Bannerblock\Block\Widget;
use Ipragmatech\Bannerblock\Helper\Data;
use Magento\Catalog\Helper\Category;

class Listwidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	/**
	 * Default value for products count that will be shown
	 */
	const DEFAULT_IMAGE_WIDTH = 250;
	const DEFAULT_IMAGE_HEIGHT = 250;
	
	protected $_helper;
	protected $categoryFlatConfig;
	protected $_categoryFactory;
	protected $_childNodes;
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
			\Magento\Catalog\Model\CategoryFactory $categoryFactory,
			\Ipragmatech\Bannerblock\Helper\Data $helper
	) {
		
		$this->_helper = $helper;
		$this->categoryFlatConfig = $categoryFlatState;
		$this->_categoryFactory = $categoryFactory;
		$this->setTemplate('widget/list.phtml');
		parent::__construct($context);
	}
	
	public function getCategoryList(){
		$selectedCategoriesId = $this->_helper->getConfig('bannerblocksection/bannerblock_setting/bannerlist');
		$categoryids = (explode(',',$selectedCategoriesId));
		try{
			if(count($categoryids)){
				$categoryCollection = $this->_categoryFactory->create()->getCollection();
				$categoryCollection->addFieldToFilter(
						'entity_id',
						['in' => $categoryids]
						);
			}
		}catch (\Exception $e){
		}
	}
	
	public function getCategory($id){
		$category = $this->_categoryFactory->create();
		if($id){
			$category->load($id);
		}
		return $category;
	}
	/**
	 * Retrieve current store categories
	 *
	 * @param bool|string $sorted
	 * @param bool $asCollection
	 * @param bool $toLoad
	 * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
	 */
	public function getCategoryCollection()
	{
		$category = $this->_categoryFactory->create();
		if($this->getData('parentcat') > 0){
			$rootCat = $this->getData('parentcat');
			$category->load($rootCat);
		}
	
		if(!$category->getId()){
			$rootCat = $this->_storeManager->getStore()->getRootCategoryId();
			$category->load($rootCat);
		}
		$storecats = $category->getChildrenCategories();
		$storecats->addAttributeToSelect('image');
		return $storecats;
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
		//$subcategories = $category->getChildren();
		$subCategoryids = (explode(',',$subcategories));
		return $subCategoryids;
	}

	public function checkChildCategories($category)
	{
		if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
			$subcategories = (array)$category->getChildrenNodes();
		} else {
			$subcategories = $category->getChildren();
		}
		if($subcategories)
			return true;
		else
			return false;
	}

	public function checkStaus($category){

		$status = $this->checkChildCategories($category);
        $arr[] = $category->getEntityId();
		if($status){
			$subcategories = $this->getChildCategories($category);
			foreach ($subcategories as $sc) {
				$temp = $this->getCategorymodel($sc);
				$this->checkStaus($temp);
			}
		}else{
           $this->_childNodes[] = $category->getEntityId();
		}

        return $this->_childNodes;
	}
    
    public function getLeafChild($category){
        $this->_childNodes = '';
        $status = $this->checkStaus($category);
        return $status;
    }
	//below planning to implement 
	/**
	 * Get the widht of product image
	 * @return int
	 */
	public function getImagewidth() {
		if($this->getData('imagewidth')==''){
			return DEFAULT_IMAGE_WIDTH;
		}
		return (int) $this->getData('imagewidth');
	}
	/**
	 * Get the height of product image
	 * @return int
	 */
	public function getImageheight() {
		if($this->getData('imageheight')==''){
			return DEFAULT_IMAGE_HEIGHT;
		}
		return (int) $this->getData('imageheight');
	}
	
	public function canShowImage(){
		if($this->getData('image') == 'image')
			return true;
			elseif($this->getData('image') == 'no-image')
			return false;
	}
	/**
	 * Return categories helper
	 */
	public function getCategoryHelper()
	{
		return $this->_categoryHelper;
	}
	
	public function getCategorymodel($id)
	{
			$_category = $this->_categoryFactory->create();
			$_category->load($id);
			return $_category;
	}
}