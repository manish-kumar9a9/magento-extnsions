<?php
/**
 * Copyright © 2015 Ipragmatech. All rights reserved.
 */
namespace Ipragmatech\Pricenegotiator\Model\ResourceModel;

/**
 * Negotiator resource
 */
class Negotiator extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('pricenegotiator_negotiator', 'id');
    }

  
}
