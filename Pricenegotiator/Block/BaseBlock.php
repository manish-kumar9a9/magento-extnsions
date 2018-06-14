<?php
/**
 * Copyright © 2015 Ipragmatech . All rights reserved.
 */
namespace Ipragmatech\Pricenegotiator\Block;
use Magento\Framework\UrlFactory;
class BaseBlock extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Ipragmatech\Pricenegotiator\Helper\Data
     */
	 protected $_devToolHelper;
	 
	 /**
     * @var \Magento\Framework\Url
     */
	 protected $_urlApp;
	 
	 /**
     * @var \Ipragmatech\Pricenegotiator\Model\Config
     */
    protected $_config;

    /**
     * @param \Ipragmatech\Pricenegotiator\Block\Context $context
	 * @param \Magento\Framework\UrlFactory $urlFactory
     */
    public function __construct( \Ipragmatech\Pricenegotiator\Block\Context $context
	)
    {
        $this->_devToolHelper = $context->getPricenegotiatorHelper();
		$this->_config = $context->getConfig();
        $this->_urlApp=$context->getUrlFactory()->create();
		parent::__construct($context);
	
    }
	
	/**
	 * Function for getting event details
	 * @return array
	 */
    public function getEventDetails()
    {
		return  $this->_devToolHelper->getEventDetails();
    }
	
	/**
     * Function for getting current url
	 * @return string
     */
	public function getCurrentUrl(){
		return $this->_urlApp->getCurrentUrl();
	}
	
	/**
     * Function for getting controller url for given router path
	 * @param string $routePath
	 * @return string
     */
	public function getControllerUrl($routePath){
		
		return $this->_urlApp->getUrl($routePath);
	}
	
	/**
     * Function for getting current url
	 * @param string $path
	 * @return string
     */
	public function getConfigValue($path){
		return $this->_config->getCurrentStoreConfigValue($path);
	}
	
	/**
     * Function canShowPricenegotiator
	 * @return bool
     */
	public function canShowPricenegotiator(){
		$isEnabled=$this->getConfigValue('pricenegotiator/module/is_enabled');
		if($isEnabled)
		{
			$allowedIps=$this->getConfigValue('pricenegotiator/module/allowed_ip');
			 if(is_null($allowedIps)){
				return true;
			}
			else {
				$remoteIp=$_SERVER['REMOTE_ADDR'];
				if (strpos($allowedIps,$remoteIp) !== false) {
					return true;
				}
			}
		}
		return false;
	}
	
}
