<?php


namespace Ipragmatech\Orders\Api;

interface OrdersitemsManagementInterface
{


    /**
     * GET for Ordersitems api
     * @param string $param
     * @return string
     */
    public function getOrdersitems($param);
    
    /**
     * Lists order items that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included. See http://devdocs.magento.com/codelinks/attributes.html#OrderItemRepositoryInterface to
     * determine which call to use to get detailed information about all attributes for an object.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @return \Magento\Sales\Api\Data\OrderItemSearchResultInterface Order item search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
