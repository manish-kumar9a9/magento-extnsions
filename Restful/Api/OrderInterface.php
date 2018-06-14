<?php

/**
 * Copyright © 2015 Ipragmatech . All rights reserved.
 */
namespace Ipragmatech\Restful\Api;

use Ipragmatech\Restful\Api\Data\PointInterface;

/**
 * Defines the service contract for some simple maths functions.
 * The purpose is
 * to demonstrate the definition of a simple web service, not that these
 * functions are really useful in practice. The function prototypes were therefore
 * selected to demonstrate different parameter and return values, not as a good
 * calculator design.
 */
interface OrderInterface
{

    /**
     * Return the order deatail.
     *
     * @api
     *
     * @param int $customerId
     *            Left hand operand.
     * @return order detail
     */
    public function myorder($customerId);

    /**
     * To verify payment
     *
     * @api
     *
     * @param int $orderId
     * @param string paymentId
     *            Left hand operand.
     * @return boolean status
     */
    public function verifyPayment($orderId, $paymentId);
}
