<?php
/**
 *
 * Copyright © 2015 Ipragmatechcommerce. All rights reserved.
 */
namespace Meggalife\Auth\Controller\Auth;

use Magento\Setup\Exception;

class Login extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
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

        //make cur req
        $userData = $this->fetchMeggaUser($params['phpsession'], $params['token']);

        //capture response
        $userName = $userData['username'];
        //$name = $userData['displayname'];
        $firstName = $userData['first_name'];
        $lastName = $userData['last_name'];
        //$photoURL = $userData['path'];
        $password = $userData['password'];
        $email = $userData['email'];

        if( $email ){
            // Get Website ID
            $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();

            $this->_customer->setWebsiteId($websiteId);
            $customer = $this->_customer->loadByEmail($email);
            if( $customer->getId()){
                //login
                $customer = $this->_customer->loadByEmail($email);
                $this->_customerSession->setCustomerAsLoggedIn($customer);
                $this->messageManager->addSuccess(__('Congratulations! You have successfully logged In.'));

            }else{
                //create and login
                $logger->info('Creating new customer');
                try {
                    // Instantiate object
                    $customerObj = $this->customerFactory->create();
                    $customerObj->setWebsiteId($websiteId);

                    // Preparing data for new customer
                    $customerObj->setEmail($email);
                    $customerObj->setFirstname($firstName);
                    $customerObj->setLastname($lastName);
                    $customerObj->setPassword($password);
                    $customerObj->setUserName($userName);
                    $customerObj->setRecoveryEmail($email);

                    // Save data
                    $customerObj->save();
                    $customerObj->sendNewAccountEmail();
                    $this->messageManager->addSuccess(__('Congratulations! You have successfully logged In.'));

                    $this->_customer->setWebsiteId($websiteId);
                    $customer = $this->_customer->loadByEmail($email);
                    $this->_customerSession->setCustomerAsLoggedIn($customer);

                }catch (\Exception $exception){
                    $logger->info( $exception->getMessage() );
                    $this->messageManager->addError($exception->getMessage());
                }

            }

        }

        //yes login
        //No create and login
        //create customer attribute username
        //$this->messageManager->addSuccess(__('Query have been sent to the vendor'));
        //$this->messageManager->addError(__('Query have been sent to the vendor'));
        //sso/auth/loginsuccess

        $this->_redirect('sso/auth/loginsuccess');
    }

    /**
     *  Curl request to Meggalife to fetch detail
     *
     */

    public function fetchMeggaUser( $session ,  $token){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/megga.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("fetchMeggaUser() :: ");

        try {
            //make curl to fetch data from session and Token
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $baseurl = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/base_url');

            $url = $baseurl."ipseserver/resource/?access_token=".$token."&phpSession=".$session;
            $logger->info( 'Curl URL:: '.$url );

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            $output=curl_exec($ch);
            if($output === false)
            {
                $this->messageManager->addError('Curl Error: '. curl_error($ch).", Error No:". curl_errno($ch));
                $this->_redirect('sso/auth/loginsuccess');
            }

            curl_close($ch);
            $resultData = json_decode($output, true);

            $logger->info(json_encode((array)$output));
            $logger->info(json_encode((array)$resultData));

            if( isset($resultData['error']) ){
                $errMsg = $resultData['error'].' '.$resultData['error_description'];
                $this->messageManager->addError($errMsg);
            }

            return $resultData;

        } catch (\Exception $e) {
            $logger->info( 'Curl Exception:: '.$exception->getMessage() );
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('sso/auth/loginsuccess');

        }
    }
}
