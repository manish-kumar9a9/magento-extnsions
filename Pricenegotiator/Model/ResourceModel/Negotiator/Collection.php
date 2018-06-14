<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ipragmatech\Pricenegotiator\Model\ResourceModel\Negotiator;

/**
 * Negotiators Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ipragmatech\Pricenegotiator\Model\Negotiator', 'Ipragmatech\Pricenegotiator\Model\ResourceModel\Negotiator');
    }
    
    public function getCollection()
    {
    	$model = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Model\Negotiator');
    	$collection = $model->getCollection();
    
    	return $collection;
    }
}
