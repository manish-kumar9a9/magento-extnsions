<?php

namespace Ipragmatech\Pricenegotiator\Block\Catalog\Product;
use Ipragmatech\Pricenegotiator\Model\NegotiatorFactory;

class Negotiateprice extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @return void
     */
	protected $_negotiatorFactory;
	protected $_objectManager;
	protected $_catalogSession;
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			NegotiatorFactory $negotiatorFactory,
			\Magento\Catalog\Model\Session $catalogSession,
			array $data = []
			) {
		parent::__construct ( $context, $data );
		$this->_objectManager = $objectManager;
		$this->_negotiatorFactory = $negotiatorFactory;
		$this->_catalogSession = $catalogSession;
	}
	public function getNegotiateInfo($productId, $customerId){
		//return status, coupan
		// Get news collection
		
		$collection = $this->_negotiatorFactory->create()->getCollection();
		$collection->addFieldToFilter(
				'product_id',
				['eq' => $productId]
				);
		$collection->addFieldToFilter(
				'customer_id',
				['eq' => $customerId]
				);
		$collection->addFieldToFilter(
				'query_status',
				['eq' => 1]
				);
		$collection->addFieldToSelect('replied_status');
		$collection->addFieldToSelect('coupan_code');
		$collection->addFieldToSelect('owner_price');
		$collection->getSelect()
		->order('id DESC')
		->limit(1);
		$offerData = array();
		foreach ($collection as $item){
			$offerData['status'] = $item['replied_status'];
			$offerData['coupon'] = $item['coupan_code'];
			$offerData['new_price'] = $item['owner_price'];
		}
		return $offerData;
	}
	public function getQueryProduct(){
		$pro =  $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');
		if($pro){
		return $pro;
		}else{
			$productId = $this->_catalogSession->getQPId();
			$pro = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productId);
			return $pro;
		}
	}
}