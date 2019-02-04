<?php

namespace Picpay\Request;

/**
 * Class NotificationRemoteRequest
 *
 * @package Picpay\Request
 */
class NotificationRemoteRequest
{
    /** @var string $authorizationId */
    private $authorizationId;

    /**
     * NotificationRequest constructor.
     *
     * @param string $requestBody
     */
    public function __construct(string $requestBody)
    {
        $requestBodyArray = json_decode($requestBody);
        
        $this->authorizationId = isset($requestBodyArray->authorizationId) ? $requestBodyArray->authorizationId : null;
    }

    /**
     * Get the value of authorizationId
     *
     * @return string
     */
    public function getAuthorizationId()
    {
        return $this->authorizationId;
    }
}
