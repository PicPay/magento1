<?php

namespace Picpay\Exception;

/**
 * Class RequestException
 *
 * @package Picpay\Exception
 */
class RequestException extends \Exception
{

    /** @var array $errors */
    private $errors;

    /**
     * RequestException constructor.
     *
     * @param string $message
     * @param int    $code
     * @param null   $previous
     * @param array  $errors
     */
    public function __construct($message, $code, $previous = null, $errors = [])
    {
        parent::__construct($message, $code, $previous);
        
        $this->errors = $errors;
    }

    /**
     * Get the value of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
