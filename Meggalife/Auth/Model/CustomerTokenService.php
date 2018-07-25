<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Meggalife\Auth\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\Token as Token;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Framework\Exception\AuthenticationException;

class CustomerTokenService implements \Magento\Integration\Api\CustomerTokenServiceInterface
{
    /**
     * Token Model
     *
     * @var TokenModelFactory
     */
    private $tokenModelFactory;

    /**
     * Customer Account Service
     *
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var \Magento\Integration\Model\CredentialsValidator
     */
    private $validatorHelper;

    /**
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    private $tokenModelCollectionFactory;

    /**
     * @var RequestThrottler
     */
    private $requestThrottler;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param AccountManagementInterface $accountManagement
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     * @param CredentialsValidator $validatorHelper
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        AccountManagementInterface $accountManagement,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->accountManagement = $accountManagement;
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
        $this->validatorHelper = $validatorHelper;
        $this->_customer = $customer;
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerAccessToken($username, $password)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/auth.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->validatorHelper->validate($username, $password);

        $this->getRequestThrottler()->throttle($username, RequestThrottler::USER_TYPE_CUSTOMER);
//        try {
//            $logger->info("Own login classsss.......try");
//            $customerDataObject = $this->accountManagement->authenticate($username, $password);
//        } catch (\Exception $e) {
//            $this->getRequestThrottler()->logAuthenticationFailure($username, RequestThrottler::USER_TYPE_CUSTOMER);
//            throw new AuthenticationException(
//                __('You did not sign in correctly or your account is temporarily disabled.')
//            );
//        }
        //vlidate from MeggaLife
        $resultData = $this->validateUserFromMeggalife($username, $password);
        $userData = $resultData['user'];
        $userName = $userData['username'];
        $firstName = $userData['username'];
        $lastName = 'lastname';
        //$photoURL = $userData['thumbnail'];
        $email    = $userData['email'];
        $customerID = '';

        if($email){
            // Get Website ID
            $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
            $this->_customer->setWebsiteId($websiteId);
            $customer = $this->_customer->loadByEmail($email);

            if( $customer->getId()){
                $customerID = $customer->getId();

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

                    $this->_customer->setWebsiteId($websiteId);
                    $customer = $this->_customer->loadByEmail($email);
                    $customerID = $customer->getId();

                }catch (\Exception $exception){
                    $logger->info( $exception->getMessage() );
                    throw new LocalizedException(__($exception->getMessage()));
                }

            }
        }else{
            throw new AuthenticationException(
                __('You did not sign in correctly or your account is temporarily disabled.')
            );
        }
        $this->getRequestThrottler()->resetAuthenticationFailuresCount($username, RequestThrottler::USER_TYPE_CUSTOMER);
        //return $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();
        return $this->tokenModelFactory->create()->createCustomerToken($customerID)->getToken();
    }

    /**
     * Revoke token by customer id.
     *
     * The function will delete the token from the oauth_token table.
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revokeCustomerAccessToken($customerId)
    {
        $tokenCollection = $this->tokenModelCollectionFactory->create()->addFilterByCustomerId($customerId);
        if ($tokenCollection->getSize() == 0) {
            throw new LocalizedException(__('This customer has no tokens.'));
        }
        try {
            foreach ($tokenCollection as $token) {
                $token->delete();
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('The tokens could not be revoked.'));
        }
        return true;
    }

    /**
     * Get request throttler instance
     *
     * @return RequestThrottler
     * @deprecated 100.0.4
     */
    private function getRequestThrottler()
    {
        if (!$this->requestThrottler instanceof RequestThrottler) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(RequestThrottler::class);
        }
        return $this->requestThrottler;
    }

    /**
     * Validate user from Megaalife
     * @param $user
     * @param $password
     */

    private function validateUserFromMeggalife($user, $password){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/auth.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        //$logger->info("validateUserFromMeggalife::........2");

        //fetch params from system configuration
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $baseurl = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/base_url');
        $url =  $baseurl . "socialapi/auth/post/login";
        $apiKey = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/meggalife_key');
        $secretKey = $objectManager->create('Meggalife\Auth\Helper\Data')->getConfig('meggalife_auth/general/meggalife_secrete');

        $logger->info($url);

        $params = array (
            'apiKey' => $apiKey , //'meggalife',  //socail api key
            'secretKey' => $secretKey, //'meggalife123', //social secrete key
            'email' => $user,
            'password' => $password,
//            'deviceId' => $deviceId,
//            'deviceType' => $deviceType
        );
        $params['clientId'] = 12;

        try{



            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$params);

            $output=curl_exec($ch);

            if($output === false)
            {
                $message = 'ERROR: '.curl_error($ch);
                throw new LocalizedException(__($message));
            }

            curl_close($ch);
            $resultData = json_decode($output, true);

            $status = isset($resultData['status']) ? $resultData['status'] : '';
            if( !$status ){

                $message = $resultData['message'];
                throw new LocalizedException(__($message));
            }

            return $resultData;

        }catch (\Exception $e) {
            //$logger->info($e);
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
