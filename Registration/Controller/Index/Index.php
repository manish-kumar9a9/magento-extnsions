<?php

/** 
 * Copyright © 2016 iPragmatech solutions. All rights reserved.
 * @author: Ajay Mehta (iPragmatech solutions ) 
**/

namespace Ipragmatech\Registration\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
class Index extends \Magento\Framework\App\Action\Action
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
    
    protected $_customerSession;
    
    protected $storeManager;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
    	\Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
    	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->resultPage = $this->resultPageFactory->create();  
        $mobileNumber = $this->_customerSession->getMobileNumber();
        
//         $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ip.log');
//         $logger = new \Zend\Log\Logger();
//         $logger->addWriter($writer);
       	if(!isset($mobileNumber) || empty($mobileNumber) || trim($mobileNumber) == ""){
       		//$logger->info('If not Mobile Number>>'.$mobileNumber."<<");
       		//$this->messageManager->addError('Please verify your mobiile');
        	$resultRedirect->setUrl($this->_storeManager->getStore()->getBaseUrl().'customer/account/create/');
        }
        else{
//        	$logger->info('Mobile Number>>'.$mobileNumber."<<");
	    	return $this->resultPage;
        }
    }
}
