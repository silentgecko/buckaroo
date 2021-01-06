<?php

namespace silentgecko\Buckaroo\SOAP;

class Invoice
{
    public $Number;

    public function __construct($Number)
    {
        $this->Number = $Number;
    }
}

