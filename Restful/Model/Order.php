<?php

/**
 * Copyright © 2015 Ipragmatech . All rights reserved.
 * 
 */
namespace Ipragmatech\Restful\Model;

use Ipragmatech\Restful\Api\OrderInterface;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class Order implements OrderInterface
{

    /**
     * @var Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     */
    protected $_transactionBuilder;

    /**
     * @param Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     * $transbuilder
     */
    public function __construct(
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
        $transbuilder
    ) {
        $this->_transactionBuilder = $transbuilder;
    }

    /**
     * function to get order history of customer
     *
     * @param int $customerId
     * @return array
     * @throws InputException
     */

    public function myorder($customerId)
    {
        

        if (empty($customerId) || ! isset($customerId) || $customerId == "") {
            throw new InputException(__('Id required'));
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orders = $objectManager->create('Magento\Sales\Model\Order')
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
            $orderData = [];
            if (count($orders)) {
                foreach ($orders as $order) {
                    $items = $order->getAllVisibleItems(); // $order->getAllItems();
                    
                    $produtsData = array();
                    foreach ($items as $item) {
                        $product = $objectManager->get('Magento\Catalog\Model\Product')->loadByAttribute('sku', $item->getSku());
                        $productdata = array(
                            'product_id' => $item->getProductId(),
                            'sku' => $item->getSku(),
                            'name' => $item->getName(),
                            'description' => $item->getDescription(),
                            'price' => $item->getPrice(),
                            'base_price' => $item->getBasePrice(),
                            'qty_ordered' => round($item->getQtyOrdered()),
                            'image' => $product->getImage(),
                            'size' => $product->getResource()
                                ->getAttribute('size')
                                ->getFrontend()
                                ->getValue($product),
                            'color' => $product->getResource()
                                ->getAttribute('color')
                                ->getFrontend()
                                ->getValue($product)
                        );
                        $produtsData[] = $productdata;
                    }
                    
                    $data = array(
                        'order_id' => $order->getEntityId(),
                        'status' => $order->getStatus(),
                        'amount' => $order->getBaseGrandTotal(),
                        'order_date' => $order->getUpdatedAt(),
                        'shipping_charge' => $order->getShippingAmount(),
                        'discount_amount' => $order->getDiscountAmount(),
                        'tax' => $order->getTaxAmount(),
                        'couponcode' => $order->getCouponCode(),
                        'qty' => $order->getTotalItemCount(),
                        'products' => $produtsData
                    );
                    $orderData[] = $data;
                }
                return $orderData;
            } else {
                return $orderData;
            }
        }
    }

    /**
     * save transaction detail
     *
     * @param int $orderId
     * @param $paymentId
     * @return array|bool
     * @throws \Zend\Log\Exception\InvalidArgumentException
     */

    public function verifyPayment($orderId, $paymentId){

         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $order = $objectManager->create('Magento\Sales\Model\Order')
            ->load($orderId);
        if(!$order){
            $response[] = [
                'status' => false,
                'message' => 'Order not found',
                //'transaction_id'    => $transactionId
            ];
            return $response;
        }

        $paymentData = [
            'id' => $paymentId
        ];

        try {
            //get payment object from order object
            $payment = $order->getPayment();
            $payment->setLastTransId($paymentData['id']);
            $payment->setTransactionId($paymentData['id']);
            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
            );
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                $order->getGrandTotal()
            );

            $message = __('The authorized amount is %1.', $formatedPrice);
            //get the object of builder class
            $trans = $this->_transactionBuilder;
            $transaction = $trans->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($paymentData['id'])
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]
                )
                ->setFailSafe(true)
                //build method creates the transaction and returns the object
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

            $payment->addTransactionCommentsToOrder(
                $transaction,
                $message
            );
            $payment->setParentTransactionId(null);
            $payment->save();
            $order->save();
            $transactionId = $transaction->save()->getTransactionId();
            $response[] =  [
                'status' => true,
                'message' => 'verified successfully',
                'transaction_id'    => $transactionId
            ];
            return $response;
        } catch (Exception $e) {
           return false;
        }
    }
}