<?php
/**
 *
 * Copyright © 2015 Ipragmatechcommerce. All rights reserved.
 */
namespace Ipragmatech\Pricenegotiator\Controller\Pricequery;
use Magento\Framework\Controller\ResultFactory;

class Checkquery extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    protected $catalogSession;
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    	\Magento\Catalog\Model\Session $catalogSession
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->catalogSession = $catalogSession;
    }
	
    /**
     * Flush cache storage
     *
     */
    
    public function execute()
    {
		$params = $this->getRequest()->getParams();
		try{
			$resultPage = $this->resultPageFactory->create();
			$block = $resultPage->getLayout()
					->createBlock('Ipragmatech\Pricenegotiator\Block\Catalog\Product\Negotiateprice')
					->setTemplate('Ipragmatech_Pricenegotiator::product/view.phtml')
			    	->toHtml();
	    	return $this->getResponse()->setBody($block);
		}catch (\Exception $e){
		}
       
    }
}
