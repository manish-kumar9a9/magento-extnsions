<?php
/**
 *
 * Copyright © 2015 Ipragmatechcommerce. All rights reserved.
 */
namespace Meggalife\Auth\Controller\Auth;

use Magento\Setup\Exception;

class Update extends \Magento\Framework\App\Action\Action
{


    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Login constructor.
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_customer = $customer;
        $this->_customerSession = $customerSession;
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
    }

    /**
     * Flush cache storage
     *
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/megga.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info(print_r($this->getRequest()->getParams(),true));
        $params = $this->getRequest()->getParams();

        die();
        //capture response
        //$user = $userData['username'];
        //$name = $userData['displayname'];
        //$firstName = $userData['first_name'];
        //$lastName = $userData['last_name'];
        //$photoURL = $userData['path'];
        //$password = $userData['password'];
        //$email = $userData['email'];

        if( $email ){
            // Get Website ID
            $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();

            $this->_customer->setWebsiteId($websiteId);
            $customer = $this->_customer->loadByEmail($email);
            if( $customer->getId()){
                //login
                $customer = $this->_customer->loadByEmail($email);

            }else{
                //create and login
                $logger->info('Creating new customer');
                try {

                    $this->_customer->setWebsiteId($websiteId);
                    $customer = $this->_customer->loadByEmail($email);

                }catch (\Exception $exception){
                    $logger->info( $exception->getMessage() );
                    $this->messageManager->addError($exception->getMessage());
                }

            }

        }

        $this->_redirect('sso/auth/loginsuccess');
    }
}
