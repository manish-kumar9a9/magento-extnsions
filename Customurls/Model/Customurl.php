<?php
/**
 * Contributor company: iPragmatech solution Pvt Ltd.
 * Contributor Author : Manish Kumar
 * Date: 22/09/16
 * Time: 6:55 PM
 */

namespace Ipragmatech\Customurls\Model;

use \Ipragmatech\Customurls\Api\UrlInterface;


/**
 * Defines the implementaiton class of the UrlInterface
 * @package Ipragmatech\Customurls\Model
 */
class Customurl implements UrlInterface
{
    /**
     * @var \Ipragmatech\Customurls\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customurl constructor.
     * @param \Ipragmatech\Customurls\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Ipragmatech\Customurls\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
    }

    /**
     * Return about us url.
     * @return string
     */
    public function getAboutusUrl(){

        $aboutUs = $this->_helper->getConfig('custom_url/general/about_us');
        $aboutUsUrl = $this->_storeManager->getStore()->getUrl($aboutUs);

        return $aboutUsUrl;
    }

    /**
     * Return contact us url.
     * @return string
     */
    public function getContactUrl(){

        $contactUs = $this->_helper->getConfig('custom_url/general/contact_us');
        $contactUsUrl = $this->_storeManager->getStore()->getUrl($contactUs);
        
        return  $contactUsUrl;
    }

    /**
     * Return customer service url.
     * @return string
     */
    public function getCustomerServiceUrl(){

        $customerService = $this->_helper->getConfig('custom_url/general/customer_service');
        $customerServiceUrl = $this->_storeManager->getStore()->getUrl($customerService);

        return $customerServiceUrl;
    }

    /**
     * Return policy url.
     * @return string
     *
     */
    public function getPolicyUrl(){

        $policy = $this->_helper->getConfig('custom_url/general/policy');
        $policyUrl = $this->_storeManager->getStore()->getUrl($policy);

        return $policyUrl;
    }
}
