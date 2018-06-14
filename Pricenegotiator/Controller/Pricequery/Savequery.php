<?php
/**
 *
 * Copyright © 2015 Ipragmatechcommerce. All rights reserved.
 */
namespace Ipragmatech\Pricenegotiator\Controller\Pricequery;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Savequery extends \Magento\Framework\App\Action\Action
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
    protected $scopeConfig;
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    	\Magento\Catalog\Model\Session $catalogSession,
    	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->catalogSession = $catalogSession;
        $this->scopeConfig = $scopeConfig;
    }
	
    /**
     * Flush cache storage
     *
     */
    
    public function execute()
    {
    	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		
		$params = $this->getRequest()->getParams();
		$data = array();
		
		if(isset($params)){
			$data['product_id']	 	= $params['neo-product-id'];
			$this->catalogSession->setQPId($data['product_id']);
			$data['product_name']	= $params['neo-product-name'];
			$data['product_sku'] 	= $params['neo-product-sku'];
			$data['original_price'] = $params['neo-product-price'];
			$data['customer_id'] 	= $params['neo-customer-id'];
			$data['customer_name']	= $params['neo-customer-name'];
			$data['customer_email'] = $params['neo-customer-email'];
			$data['customer_price'] = $params['customer-price'];
			
			$data['customer_message'] = $params['customer-message'];
			$idt = date("Y-m-d H:i:s");
			$data['created_at']	  = $idt;
			$data['query_status'] = 1;
		}
		
		if ($data) {
			$model = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Model\Negotiator');
			
			$model->setData($data);
				
			try {
				$model->save();
				
				/*sending mail to admin */
				
				$storeEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
				$storeName  = $this->scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
				
				
				$enableAdminNotification = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Helper\Data')->getConfig('pricenegotiatorsection/negotiator_notification/admin_notify');
				$receiverEmail= $this->_objectManager->create('Ipragmatech\Pricenegotiator\Helper\Data')->getConfig('pricenegotiatorsection/negotiator_notification/admin_email');
				if($enableAdminNotification){
					$receiverInfo = [
							'name' => 'Admin',
							'email' => $receiverEmail,
					];
					$senderInfo = [
							'name' => $storeName,
							'email' => $storeEmail,
					];
					$emailTemplateVariables = array();
					$emailTemplateVariables['myvar1'] = $data['product_id'];
					$emailTemplateVariables['myvar2'] = $data['product_sku'];
					$emailTemplateVariables['myvar3'] = $data['customer_name'];
					$emailTemplateVariables['myvar4'] = $data['customer_email'];
					$emailTemplateVariables['myvar5'] = $data['customer_message'];
					$emailTemplateVariables['myvar6'] = $data['customer_price'];
					$emailTemplateVariables['myvar7'] = $data['original_price'];
					$emailTemplateVariables['myvar8'] = $data['created_at'];
					
					/* call send mail method from helper*/
					$this->_objectManager->get('Ipragmatech\Pricenegotiator\Helper\Email')->notifyAdminQuery(
							$emailTemplateVariables,
							$senderInfo,
							$receiverInfo
							);
				}
				/*sending mail to admin End*/
				
				$this->messageManager->addSuccess(__('Query have been sent to the vendor'));
			} catch (\Magento\Framework\Model\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
			}
				
		}
		try{
			$resultPage = $this->resultPageFactory->create();
			$block = $resultPage->getLayout()
			->createBlock('Ipragmatech\Pricenegotiator\Block\Catalog\Product\Negotiateprice')
			->setTemplate('Ipragmatech_Pricenegotiator::product/view.phtml')
	    	->toHtml();
			
	    	return $this->getResponse()->setBody($block);
		}catch (\Exception $e){
			$this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
		}
       
    }
}
