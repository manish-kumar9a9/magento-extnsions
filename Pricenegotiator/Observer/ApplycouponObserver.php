<?php
/**
 * Copyright © 2015 Ipragmatech. All rights reserved.
 */
namespace Ipragmatech\Pricenegotiator\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ipragmatech\Pricenegotiator\Model\NegotiatorFactory;

class ApplycouponObserver implements ObserverInterface
{
	/**
	 * @var \Magento\SalesRule\Model\Coupon
	 */
	protected $_coupon;
	
	protected $_objectManager;
	/**
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 */
	public function __construct(
			\Magento\Framework\ObjectManagerInterface $objectManager,
			NegotiatorFactory $coupon
			) {
				$this->_objectManager = $objectManager;
				$this->_coupon = $coupon;
	}
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	
    	try{
    		$enableNegotiator = $this->_objectManager->create('\Ipragmatech\Pricenegotiator\Helper\Data')->getConfig('pricenegotiatorsection/general/active');
	    	if($enableNegotiator){
				$order = $observer->getEvent()->getOrder();
				
				$items =$order->getAllVisibleItems();
				$productId ='';
				foreach($items as $item) {
					$productId= $item->getProductId();
				}
				
				$cc = $order->getCouponCode();
				$customerId = $order->getCustomerId();
				if($cc){
					$collection = $this->_coupon->create()->getCollection();
					$collection->addFieldToFilter(
							'coupan_code',
							['eq' => $cc]
							);
					$collection->addFieldToFilter(
							'product_id',
							['eq' => $productId ]
							);
					$collection->addFieldToFilter(
							'customer_id',
							['eq' => $customerId ]
							);
					$id = '';
					if(count($collection)){
						foreach ($collection as $item){
							$id = $item['id'];
						}
						$model = $this->_objectManager->create('Ipragmatech\Pricenegotiator\Model\Negotiator');
						if ($id) {
							$model->load($id);
							try{
								$model->setQueryStatus(2);
								$model->save();
							}catch (\Exception  $e){
								$this->messageManager->addException($e, __('Something went wrong while saving the coupan status.'));
							}
						}
					}
				}
			}
    	}catch (\Exception $e){
    		$this->messageManager->addError( __('Something went wrong while saving the coupan status.'));
    		
    	}
    }
}