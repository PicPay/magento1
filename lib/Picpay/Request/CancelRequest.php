<?php

namespace Picpay\Request;

use Picpay\Seller;
use GuzzleHttp\Psr7\Request;

/**
 * Class CancelRequest
 *
 * @package Picpay\Request
 */
class CancelRequest extends BaseRequest
{
    /** @var string $referenceId */
    private $referenceId;

    /** @var string $authorizationId */
    private $authorizationId;

    /**
     * StatusRequest constructor.
     *
     * @param Seller $seller
     * @param string $referenceId
     * @param string $authorizationId
     */
    public function __construct(Seller $seller, $referenceId, $authorizationId)
    {
        parent::__construct($seller);

        $this->referenceId = $referenceId;
        $this->authorizationId = $authorizationId;
    }

    /**
     * Make the request to be sent
     *
     * @return GuzzleHttp\Psr7\Request
     */
    public function makeRequest()
    {
        return new Request('POST', "payments/{$this->referenceId}/cancellations", [], json_encode(['authorizationId' => $this->authorizationId]));
    }

    /**
    * Read the response and return cancellationId
    *
    * @param mixed $reponseBody
    *
    * @return mixed
    */
    public function readResponse($reponseBody)
    {
        return isset($reponseBody->cancellationId) ? $reponseBody->cancellationId : null;
    }
}
