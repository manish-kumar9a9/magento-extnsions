<?php
/**
 *
 * Copyright © 2015 Ipragmatechcommerce. All rights reserved.
 */
namespace Meggalife\Auth\Controller\Auth;

use Magento\Setup\Exception;

class Test extends \Magento\Framework\App\Action\Action
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

        $logger->info('test controller......');
        //$logger->info(print_r($this->getRequest()->getParams(),true));
        //$params = $this->getRequest()->getParams();

        //capture response
        //$user = $userData['username'];
        //$name = $userData['displayname'];
        //$firstName = $userData['first_name'];
        //$lastName = $userData['last_name'];
        //$photoURL = $userData['path'];
        //$password = $userData['password'];
        //$email = $userData['email'];

        $this->addPointToMeggalife();

    }

    /**
     * // Register customer
     */
    public function addPointToMeggalife( $params ){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/point.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Initiating CURL to add point of customer');

        //$url, $apiKey, $secretKey, $email, $userName, $type, $point, $additionalParams){
        $params = array(
            "username"    => $userName,
            'apiKey' => $apiKey, //"meggalife",
            'secretKey' => $secretKey, //"meggalife123",
            'api_key' => $apiKey,
            'secret_Key' => $secretKey,
            "objectType"    => null,
            "object_type"    => null,
            "objectId"    => 0,
            "object_id"    => 0,
            "points"    => $point,
            "body"    => $type,
            "actionType"    => "allocate_central_point",
            "action_type"    => "allocate_central_point",
            "additionalParams"    => $additionalParams,
            "additional_params"    => $additionalParams,
            "source"    => 10,
            "sourceName"    => "meggamore",
            "source_name"    => "meggamore",
            'version' => "1.0.1",
            'phpSessionid' => "e4dbf3m7qgr5ck4gbh57m3qmu6",
        );

        //if url sls encode it
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);

        //specify headers incase of sls approach
        //if( $result['approach'] == 'sls' ){
        //   curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //        'Content-Type: application/json',
        //       'Content-Length:' . $datalength
        //    ));
        //}

        try{
            $output=curl_exec($ch);
            //$this->wh_log_array($output);

            if($output === false)
            {
                $this->wh_log( $msg." ".curl_error($ch) );
                die(sprintf(__("%s ERROR: Curl error with %s "), curl_error($ch), curl_errno($ch)));
            }
            curl_close($ch);
            $resultData = json_decode($output, true);
            $this->wh_log_array($resultData);

            if( isset($resultData['error']) ){
                $msg .= isset($resultData['error_description']) ? $resultData['error_description'] : $resultData['message'] ;
                $this->wh_log( $msg );
                //die(sprintf(__("%s : %s "), $resultData['error'], $resultData['message'] ));
                return false;
            }elseif( isset( $resultData['errorMessage'])){
                $msg .= $resultData['errorMessage'];
            }elseif ($resultData['status'] != 1 ) {
                $msg .= "Failed to add point for Email: ". $email .", User: ". $userName .", Type: ". $type .", Point: ". $point.", Response Message:". $resultData['message'];
            }else{
                $msg .= "Point Added success for Email: ". $email .", User: ". $userName .", Type: ". $type .", Point: ". $point.", Response Message:". $resultData['message'];
            }
            $this->wh_log( $msg );
        }catch(\Exception $e){
            $logger->info(" Curl Register Excption:". $e->getMessage());
        }

    }

}
