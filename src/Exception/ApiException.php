<?php

namespace Vendloop\Exception;

class ApiException extends VendloopException
{
    private $responseObject;
    private $requestObject;

    public function __construct($message, $responseObject, $requestObject)
    {
        parent::__construct($message);
        $this->responseObject = $responseObject;
        $this->requestObject = $requestObject;
    }

    public function getResponseObject()
    {
        return $this->responseObject;
    }

    public function getRequestObject()
    {
        return $this->requestObject;
    }
}
