<?php
/**
 * Copyright © 2016 Ipragmatech . All rights reserved.
 * Author : Manish Kumar
 * Date: 04 july 2016
 */
namespace Ipragmatech\Redirection\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper

{

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(\Magento\Framework\App\Helper\Context $context
	) {
		parent::__construct($context);
	}

	public function getConfig($configpath)
	{
		return $this->scopeConfig->getValue($configpath,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}