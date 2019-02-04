<?php

namespace Picpay\Request;

use Picpay\Seller;
use GuzzleHttp\Psr7\Request;

/**
 * Class StatusRequest
 *
 * @package Picpay\Request
 */
class StatusRequest extends BaseRequest
{
    /** @var string $referenceId */
    private $referenceId;

    /**
     * StatusRequest constructor.
     *
     * @param Seller $seller
     * @param string $referenceId
     */
    public function __construct(Seller $seller, $referenceId)
    {
        parent::__construct($seller);

        $this->referenceId = $referenceId;
    }

    /**
     * Make the request to be sent
     *
     * @return GuzzleHttp\Psr7\Request
     */
    public function makeRequest()
    {
        return new Request('GET', "payments/{$this->referenceId}/status");
    }

    /**
    * Read the response and return status
    *
    * @param mixed $reponseBody
    *
    * @return mixed
    */
    public function readResponse($reponseBody)
    {
        return isset($reponseBody->status) ?  $reponseBody->status : null;
    }
}
