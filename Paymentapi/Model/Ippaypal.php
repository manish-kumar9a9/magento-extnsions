<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ipragmatech\Paymentapi\Model;



/**
 * Pay In Store payment method model
 */
class Ippaypal extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'ippaypal';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;



    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

}
