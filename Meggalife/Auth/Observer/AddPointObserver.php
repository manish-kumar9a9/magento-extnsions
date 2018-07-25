<?php

namespace Meggalife\Auth\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Meggalife\Auth\Helper\Data;

class AddPointObserver implements ObserverInterface {


    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Order Model
     *
     * @var \Magento\Sales\Model\Order $order
     */
    protected $order;

    /** * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderModel;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Meggalife\Auth\Helper\Data
     */

    protected $helper;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\OrderFactory $orderModel,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Meggalife\Auth\Helper\Data $helper,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_objectManager = $objectmanager;
        $this->order = $order;
        $this->orderModel = $orderModel;
        $this->orderSender = $orderSender;
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->customer = $customer;
        $this->storeManager = $storeManager;
    }


    public function execute(\Magento\Framework\Event\Observer $observer) {


        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/point.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $event = $observer->getEvent();
        $order = $observer->getEvent()->getOrder();
        $orderids = $observer->getEvent()->getOrderIds();

        $apiKey     = $this->helper->getConfig('meggalife_auth/general/meggalife_key');
        $secretKey  = $this->helper->getConfig('meggalife_auth/general/meggalife_secrete');
        $version    = $this->helper->getConfig('meggalife_auth/general/meggalife_version');



        foreach($orderids as $orderid){

            $smsParams = [];
            $order = $this->order->load($orderid);
            //$logger->info('Order Data: '.json_encode($order->getData()));
            $items =$order->getAllItems();
            $scheduledDate = $order->getMaxScheduleDate();
            $incrementId = $order->getIncrementId();
            $guestCustomer = $order->getCustomerIsGuest();


            //$timezoneInterface = $this->_objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            //$bdate = $timezoneInterface->date($order->getMaxDob())->format('m/d/Y');

            //add point to customer if not guest
            if( !$guestCustomer ){

                $shippingAddress = $order->getShippingAddress();
                $email = ( $order->getCustomerEmail() ) ? $order->getCustomerEmail() : $shippingAddress->getEmail() ;
                // Get Website ID
                $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
                $this->customer->setWebsiteId($websiteId);
                $customer = $this->customer->loadByEmail($email);

                $userName = $customer->getUserName();

                $point = $this->helper->getConfig('meggalife_point/point/order_success');
                $type = 'Checkout';
                $additionalParams = json_encode(array("name" =>"checkout", "link" => null,"message"=> "Order Placed" ));
                $params = array(
                    'username'    => $userName,
                    'apiKey' => $apiKey,
                    'secretKey' => $secretKey,
                    'api_key' => $apiKey,
                    'secret_Key' => $secretKey,
                    'objectType'    => null,
                    'object_type'    => null,
                    'objectId'    => 0,
                    'object_id'    => 0,
                    'points'    => $point,
                    'body'    => $type,
                    'actionType'    => 'allocate_central_point',
                    'action_type'    => 'allocate_central_point',
                    'additionalParams'    => $additionalParams,
                    'additional_params'    => $additionalParams,
                    'source'    => 12,
                    'sourceName'    => 'meggamore',
                    'source_name'    => 'meggamore',
                    'version' => $version,
                    'phpSessionid' => "e4dbf3m7qgr5ck4gbh57m3qmu6",
                );

                $logger->info('payload to Add Point:' . json_encode($params));
                $this->addPointToMeggalife($email, $params);
            }
        }

    }

    /**
     * Curl req to add point for customer
     */
    public function addPointToMeggalife( $email, $params ){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/point.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        //$url = $result['approach'] == 'mannual' ? $result['url'] : $result['sls_url'];
        $url = $this->helper->getConfig('meggalife_auth/general/base_url') . "socialapi/member/post/addpoints";
        $logger->info($url);

        //if url sls encode it
        $data_string = $params;
        //$data_string = $result['approach'] == 'mannual' ? $params : json_encode($params);
        //$datalength  = strlen(json_encode($params));

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

            if($output === false)
            {
                $msg = "ADD POINT EX: ". curl_errno($ch) . curl_error($ch);
                $logger->info($msg);
            }

            curl_close($ch);
            $resultData = json_decode($output, true);
            $logger->info(json_encode($resultData));

            if( isset($resultData['error']) ){
                $msg .= isset($resultData['error_description']) ? $resultData['error_description'] : $resultData['message'] ;
                $logger->info($msg);
            }elseif( isset( $resultData['errorMessage'])){
                $msg .= $resultData['errorMessage'];
            }elseif ($resultData['status'] != 1 ) {
                $msg .= "Failed to add point for Email: ". $email .", User: ". $params['username'] .", Type: ". $params['type'] .", Point: ". $params['point'].", Response Message:". $resultData['message'];
            }else{
                $msg .= "Point Added success for Email: ". $email .", User: ". $params['username'] .", Type: ". $params['type'] .", Point: ". $params['point'].", Response Message:". $resultData['message'];
            }
            $logger->info($msg);

        }catch(\Exception $e){
            $logger->info(" Curl Add point Excption:". $e->getMessage());
        }

    }
}
