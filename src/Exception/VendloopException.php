<?php

namespace Vendloop\Exception;

class VendloopException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
