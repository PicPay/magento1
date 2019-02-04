<?php

namespace Picpay\Request;

use Picpay\Seller;
use Picpay\Payment;
use GuzzleHttp\Psr7\Request;

/**
 * Class PaymentRequest
 *
 * @package Picpay\Request
 */
class PaymentRequest extends BaseRequest
{
    /** @var Payment $payment */
    private $payment;

    /**
     * PaymentRequest constructor.
     *
     * @param Seller $seller
     * @param Payment $payment
     */
    public function __construct(Seller $seller, Payment $payment)
    {
        parent::__construct($seller);

        $this->payment = $payment;
    }

    /**
     * Make the request to be sent
     *
     * @return GuzzleHttp\Psr7\Request
     */
    public function makeRequest()
    {
        return new Request('POST', 'payments', [], json_encode($this->payment));
    }

    /**
    * Read the response and return authorizationId
    *
    * @param mixed $reponseBody
    *
    * @return mixed
    */
    public function readResponse($reponseBody)
    {
        return isset($reponseBody->paymentUrl) ? $reponseBody->paymentUrl : null;
    }
}
