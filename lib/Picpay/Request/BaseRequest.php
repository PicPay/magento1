<?php

namespace Picpay\Request;

use Picpay\Seller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Picpay\Exception\RequestException as PicpayRequestException;

/**
 * Class BaseRequest
 *
 * @package Picpay\Request
 */
abstract class BaseRequest
{

    /** @var Client $client */
    protected $client;

    /**
     * BaseRequest constructor.
     *
     * @param Seller $seller
     */
    public function __construct(Seller $seller)
    {
        $this->client = new Client([
            'base_uri' => $seller->getApiUrl(),
            'headers' => [
                'Content-Type' => 'application/json',
                'x-picpay-token' => $seller->getPicpayToken(),
            ],
        ]);
    }
    
    /**
     * Run the request and return the the response unserialized
     *
     * @param array $param
     *
     * @return mixed
     *
     */
    public function execute()
    {
        try {
            $this->client->send($this->makeRequest());
        } catch (GuzzleRequestException $e) {
            $code = -1;
            $message = 'Request Error';
            $errors = [];
    
            if ($response = $e->getResponse()) {
                $code = $response->getStatusCode();

                $body = json_decode((string) $response->getBody());

                $message = isset($body->message) ? $body->message : $message;

                $errors = isset($body->errors) ? $body->errors : $errors;
            }
    
            throw new PicpayRequestException($message, $code, $e, $errors);
        }
        

        return $this->readResponse(json_decode((string) $response->getBody()));
    }

    /**
     * Make the request to be sent
     *
     * @return GuzzleHttp\Psr7\Request
     *
     */
    abstract protected function makeRequest();

    /**
    * Read the response and return it serialized
    *
    * @param mixed $responseBody
    *
    * @return mixed
    */
    abstract protected function readResponse($responseBody);
}
