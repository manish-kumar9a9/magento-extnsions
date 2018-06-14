<?php
/**
 *
 * Copyright © 2017 ipragmatech, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ipragmatech\Orders\Model\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\ProductOptionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Catalog\Api\Data\ProductOptionExtensionFactory;


class OrderRepository
{


    /** @var \Magento\Sales\Api\Data\OrderExtensionFactory */
    protected $orderExtensionFactory;

    /** @var \Magento\Sales\Api\Data\OrderItemExtensionFactory */
    protected $orderItemExtensionFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_currentProductCustomOptions;

    /**
     * @var ProductOptionProcessorInterface[]
     */
    protected $processorPool;

    /**
     * @var ProductOptionFactory
     */
    protected $productOptionFactory;

    /**
     * @var ProductOptionExtensionFactory
     */
    protected $extensionFactory;


    /**
     * Init plugin
     *
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     * @param \Magento\Sales\Api\Data\OrderItemExtensionFactory $orderItemExtensionFactory
     * @param ProductOptionFactory $productOptionFactory
     * @param ProductOptionExtensionFactory $extensionFactory


     * @param array $processorPool
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Magento\Sales\Api\Data\OrderItemExtensionFactory $orderItemExtensionFactory,
        ProductOptionFactory $productOptionFactory,
        ProductOptionExtensionFactory $extensionFactory,
        array $processorPool = []
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
        $this->processorPool = $processorPool;
        $this->productOptionFactory = $productOptionFactory;
        $this->extensionFactory = $extensionFactory;

    }

    /**
     * Get gift message
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $resultOrder
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    ) {

    	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Ipragmatech Plugin for order get");


        $resultOrder = $this->getProductoptions($resultOrder);
        //$resultOrder = $this->getOrderItemGiftMessage($resultOrder);
       // $logger->info(json_encode($resultOrder->getData()));
       $parentsProductArray = [];
        foreach ($resultOrder->getItems() as $key => $item) {
            $parentsProductArray [$item->getItemId()] = $item->getProductId();
            if($item->getParentItemId()){
                if(array_key_exists($item->getParentItemId(), $parentsProductArray)){
                    $logger->info('Parent Item ID'.$item->getItemId());
                    $logger->info(json_encode($parentsProductArray));
                    $logger->info('Parent Item ID'.$item->getParentItemId());
                    $logger->info('Parent Item ID'.$item->getItemId());
                    //unset$resultOrder
                    unset($resultOrder[$key]);
                }
            }
        }
        return $resultOrder;
    }
    /**
     * Get product custom options for items of order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    protected function getProductoptions(
    	\Magento\Sales\Api\Data\OrderInterface $order
	){

	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylog.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
	$logger->info(json_encode($order->getItems()));

        $parentsProductArray=[];
        foreach ($order->getItems() as $item) {

            $parentsProductArray [$item->getItemId()] = $item->getProductId();
            if($item->getParentItemId()){
                if(array_key_exists($item->getParentItemId(), $parentsProductArray)){
                    $logger->info('Parent Item ID'.$item->getItemId());
                    $logger->info(json_encode($parentsProductArray));
                    $logger->info('Parent Item ID'.$item->getParentItemId());
                    $logger->info('Parent Item ID'.$item->getItemId());
                    continue;
                }
            }

            $options = $item->getProductOptions();
            $purchasedOptions = [];
            $logger->info("******".$item->getProductId());
            if(array_key_exists('options', $options)){
                $purchasedOptions = $options['options'];
            }

        	$id = $item->getProductId(); //product id
        	$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$_product = $_objectManager->get('\Magento\Catalog\Model\Product')->load($id);
			$logger->info("____________________________________".$id);
			if($_product->getOptions()){
                foreach ($purchasedOptions as $optionData) {
        			foreach ($_product->getOptions() as $o) {
                        if($optionData['option_id'] == $o->getOptionId()){
            				$csoptions = [];
            				$csoptions['product_sku'] = $o->getSku();
            				$csoptions['option_id'] = $o->getOptionId();
            				$csoptions['title'] = $o->getTitle();
            				$csoptions['type'] = $o->getType();
            				$csoptions['sort_order'] = $o->getSortOrder();
            				$csoptions['is_require'] = $o->getIsRequire();
            			    foreach ($o->getValues() as $value) {
                                if($optionData['option_value']  == $value->getOptionTypeId()){
            			            $csoptions['value'][] = $value->getData();
                                }
            			    }
            				$this->_currentProductCustomOptions[] = $csoptions;
                        }
        			}
                }
                $this->addProductOption($item);
            }
        }

        return $order;
    }

     /**
     * Add product option data
     *
     * @param OrderItemInterface $orderItem
     * @return $this
     */
    protected function addProductOption(OrderItemInterface $orderItem)
    {
    	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylog.log');
	    $logger = new \Zend\Log\Logger();
	    $logger->addWriter($writer);
	    //$logger->info("_______________________addProductOption");

        /** @var DataObject $request */
        // $request = $orderItem->getBuyRequest();
        // $productType = $orderItem->getProductType();

        // if (isset($this->processorPool[$productType])
        //     && !$orderItem->getParentItemId()) {
        // 	//$logger->info("________________IP addProductOption 1");
        //     $data = $this->processorPool[$productType]->convertToProductOption($request);
        //     //$logger->info("_____________".json_encode($data['configurable_item_options'][0]->getData(), true));
        //     if ($data) {
        //         $this->setProductOption($orderItem, $data);
        //     }
        // }

        // if (isset($this->processorPool['custom_options'])
        //     && !$orderItem->getParentItemId()) {

        //     //$data = $this->processorPool['custom_options']->convertToProductOption($request);
        // 	$data = $orderItem->getProductOptions();

        //     //$this->processorPool['custom_options']->convertToProductOption($request);
        //     $temp = [
        //     	'custom_options' => $this->_currentProductCustomOptions //$data['options']
        //     ];
        //     if ($data) {
        //         $this->setProductOption($orderItem, $temp);
        //     }
        // }

		$temp = [
            	'custom_options' => $this->_currentProductCustomOptions //$data['options']
            ];
        $this->setProductOption($orderItem, $temp);
        return $this;
    }

    /**
     * Set product options data
     *
     * @param OrderItemInterface $orderItem
     * @param array $data
     * @return $this
     */
    protected function setProductOption(OrderItemInterface $orderItem, array $data)
    {
    	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylog.log');
	    $logger = new \Zend\Log\Logger();
	    $logger->addWriter($writer);
	   // $logger->info("__________________________________Set product option");


        $productOption = $orderItem->getProductOption();
        if (!$productOption) {
            $productOption = $this->productOptionFactory->create();
            $orderItem->setProductOption($productOption);
        }

        $extensionAttributes = $productOption->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->extensionFactory->create();
            $productOption->setExtensionAttributes($extensionAttributes);
        }

        $extensionAttributes->setData(key($data), current($data));

        return $this;
    }

    /**
     * Retrieve order item's buy request
     *
     * @param OrderItemInterface $entity
     * @return DataObject
     */
    protected function getBuyRequest(OrderItemInterface $entity)
    {

        $request = $this->objectFactory->create(['qty' => $entity->getQtyOrdered()]);

        $productType = $entity->getProductType();

        if (isset($this->processorPool[$productType])
            && !$entity->getParentItemId()) {
            $productOption = $entity->getProductOption();
            if ($productOption) {
                $requestUpdate = $this->processorPool[$productType]->convertToBuyRequest($productOption);
                $request->addData($requestUpdate->getData());
            }
        }

        if (isset($this->processorPool['custom_options'])
            && !$entity->getParentItemId()) {
            $productOption = $entity->getProductOption();
            if ($productOption) {
                $requestUpdate = $this->processorPool['custom_options']->convertToBuyRequest($productOption);
                $request->addData($requestUpdate->getData());
            }
        }

        return $request;
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 100.2.0
     * @return CollectionProcessorInterface
     */
    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface::class
            );
        }
        return $this->collectionProcessor;
    }

     /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
    ) {
        /** @var  $order */
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }
        return $resultOrder;
    }

}
