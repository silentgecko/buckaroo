<?php

namespace silentgecko\Buckaroo\SOAP;

class CustomParameter
{
    public $_;
    public $Name;

    public function __construct($Name, $Value)
    {
        $this->Name = $Name;
        $this->_ = $Value;
    }
}
