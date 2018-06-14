<?php
/**
 * Copyright © 2016 Ipragmatech. All rights reserved.
 * Author: Manish Kumar
 * Date: 04 july 2016
 *
 */

namespace Ipragmatech\Redirection\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CustomerloginObserver implements ObserverInterface
{

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $_objectManager;


    /**
     * @param \Magento\Framework\UrlInterface $url
     */
    protected $_url;

    /**
     * @param \Ipragmatech\Registration\Helper\Data $helper
     *
     */
     protected $_helper;

    /**
     * CustomerloginObserver constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Ipragmatech\Registration\Helper\Data $helper
     */

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $url,
        \Ipragmatech\Registration\Helper\Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_url = $url;
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	
    	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylog.log'); $logger = new \Zend\Log\Logger();
    	$logger->addWriter($writer);
        try {
            $customer = $observer->getEvent()->getData('customer');
            $customerGroup = $customer->getGroupId();

            //getting config values
            $pluginStatus = $this->_helper->getConfig('customredirectionsection/general/active');
            $configCustomerGroup = $this->_helper->getConfig('customredirectionsection/customer_redirection_grp/customer_group');
            $lgnRdnStatus = $this->_helper->getConfig('customredirectionsection/login_redirection_grp/login_redirection');
            $rdnPath = $this->_helper->getConfig('customredirectionsection/login_redirection_grp/login_redirection_path');

            $RedirectionUrl = $this->_url->getUrl($rdnPath);
            $lasturl = $this->_url->getCurrentUrl();
            $CustomRedirectionUrl = trim( $RedirectionUrl, "/" );
            $logger->info("url___".$lasturl);
            $logger->info("url1___".$CustomRedirectionUrl);
            if($pluginStatus){
                if($lgnRdnStatus){
                    if(!preg_match("#customer/account/create#", $lasturl)) {
                        if ($configCustomerGroup) {
                            if ($configCustomerGroup == $customerGroup) {
                                $this->_redirect($CustomRedirectionUrl);
                            }
                        } else {
                            $this->_redirect($CustomRedirectionUrl);
                        }
                    }
                }
            }
        }catch (\Exception $e) {
            $this->messageManager
                ->addError(__('Something went wrong while
                 redirecting after login.'));
        }
    }

    /**
     *
     * @param $url
     * @return void
     *
     */
    protected function _redirect($url){
        $this->_objectManager->get('Magento\Customer\Model\Session')
            ->setBeforeAuthUrl($url);
    }
}
