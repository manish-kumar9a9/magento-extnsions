<?php

/** 
 * Copyright © 2016 iPragmatech solutions. All rights reserved.
 * @author: Ajay Mehta (iPragmatech solutions ) 
**/

namespace Ipragmatech\Registration\Controller\Index;
use Magento\Framework\Controller\ResultFactory;

class Sendotp extends \Magento\Framework\App\Action\Action
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
    
    protected $storeManager;
	
    protected $_customerSession;
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
        $this->_storeManager=$storeManager;
        $this->_customerSession = $customerSession;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
    	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ip.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		
		$params = $this->getRequest()->getParams();
		$mobileNumber = str_replace(' ', '', $params['mobilenumber']); //'+91 88020 45390';// trim($params['mobilenumber']);
		
		if (empty($mobileNumber) || !isset($mobileNumber) || $mobileNumber == ""){
			//$logger->info('account sid:'.$accountSid.">>token".$authToken.">>number>>".$twilioNumber);
			$this->messageManager->addError('Mobile Number not valid');
			$resultRedirect->setUrl($this->_storeManager->getStore()->getBaseUrl().'customer/account/create/');
		}
		else{
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$customers = $objectManager->create('Magento\Customer\Model\Customer')->getCollection()->addFieldToFilter('mobilenumber',array ('like' => '%' . $mobileNumber . '%'));
			if (!count($customers)){
				try {
					$otp = rand(100000,999999);
					$modelOtp = $this->_objectManager->create('Ipragmatech\Registration\Model\Otp')->getCollection()
								->addFieldToFilter('mobile_number',array ('like' => '%' . $mobileNumber . '%'));
					//$logger->info('Mobile Query>>'.$modelOtp->getSelect());
					if(count($modelOtp)){
						foreach ($modelOtp as $model){
							$model->setOtp($otp);
							$model->save();
						}
					}
					else{
						$modelOtp = $this->_objectManager->create('Ipragmatech\Registration\Model\Otp');
						$modelOtp->setOtp($otp);
						$modelOtp->setCustomerId(0);
						$modelOtp->setMobileNumber($mobileNumber);
						$modelOtp->setStatus(0);
						$modelOtp->save();
					}
					//send OTP on mobile code
					$accountSid = $this->_objectManager->create('Ipragmatech\Registration\Helper\Data')->getConfig('twiliosetting/frontend_display/account_sid');
					$authToken = $this->_objectManager->create('Ipragmatech\Registration\Helper\Data')->getConfig('twiliosetting/frontend_display/auth_token');
					$twilioNumber = $this->_objectManager->create('Ipragmatech\Registration\Helper\Data')->getConfig('twiliosetting/frontend_display/twilio_number');
					$fromNumber = $mobileNumber ; //'+91 88020 45390';
					$message = "Your One Time Password is ".$otp;
					//$logger->info('account sid:'.$accountSid.">>token".$authToken.">>number>>".$twilioNumber);
					//Step 3: instantiate a new Twilio Rest Client
					$client = new \Services_Twilio($accountSid,$authToken);
					
					try{
						$sms = $client->account->messages->sendMessage(
						 		$twilioNumber,
								$fromNumber,
						 		$message
						);
						$this->_customerSession->setMobileNumber($mobileNumber);
						$resultRedirect->setUrl($this->_storeManager->getStore()->getBaseUrl().'registration/index/index');
					}
					catch (\Exception $e) {
						$this->messageManager->addException($e, __('We can\'t send the otp, something went wrong'));
						$resultRedirect->setUrl($this->_redirect->getRefererUrl());
					}
				}
				catch (\Exception $e) {
		            $this->messageManager->addException($e, __('We can\'t send the otp, something went wrong'));
		        }
			}
			else{
				$this->messageManager->addError('Mobile Number already exist');
				$resultRedirect->setUrl($this->_redirect->getRefererUrl());
			}
		}
		return $resultRedirect;
        
    }
   
}
