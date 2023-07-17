<?php

namespace Vendloop\HttpClient;

use \Vendloop\Exception\ApiException;
use \Vendloop\Exception\VendloopException;

class Response
{
    public $okay;
    public $body;
    public $messages = [];

    private $requestObject;

    public function setRequestObject($requestObject)
    {
        $this->requestObject = $requestObject;
    }

    public function getRequestObject()
    {
        return $this->requestObject;
    }

    private function parseResponse()
    {
        $resp = \json_decode($this->body);

        if ($resp === null || !property_exists($resp, 'status')) {
            throw new ApiException(
                "API Request failed with response: '" . $this->messageFromApiJson($resp)."'",
                $resp,
                $this->requestObject
            );
        }

        return $resp;
    }

    private function messageFromApiJson($resp)
    {
        $message = $this->body;
        if ($resp !== null) {
            if (property_exists($resp, 'message')) {
                $message = $resp->message;
            }
            if (property_exists($resp, 'errors') && ($resp->errors instanceof \stdClass)) {
                $message .= "\nErrors:\n";
                foreach ($resp->errors as $field => $errors) {
                    $message .= "\t" . $field . ":\n";
                    foreach ($errors as $_unused => $error) {
                        $message .= "\t\t" . $error->rule . ": ";
                        $message .= $error->message . "\n";
                    }
                }
            }
        }
        return $message;
    }

    private function implodedMessages()
    {
        return explode("\n\n", $this->messages);
    }

    public function wrapUp()
    {
        if ($this->okay) {
            return $this->parseResponse();
        } else {
			throw new VendloopException($this->implodedMessages());
        }
        error_log($this->implodedMessages());
        return false;
    }
}
