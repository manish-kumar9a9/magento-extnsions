<?php
/**
 * Copyright © 2015 Ipragmatech . All rights reserved.
 */
namespace Ipragmatech\Customurls\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(\Magento\Framework\App\Helper\Context $context
	) {
		parent::__construct($context);
	}

	/**
	 * @param $configpath
	 * @return mixed
	 */
	public function getConfig($configpath)
	{
		return $this->scopeConfig->getValue($configpath,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}