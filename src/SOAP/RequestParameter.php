<?php

namespace silentgecko\Buckaroo\SOAP;

class RequestParameter
{
    // phpcs:disable
    public $_;
    // phpcs:enable
    public $Name;
    public $Group;

    public function __construct($Name, $Value, $Group = null)
    {
        $this->Name = $Name;
        $this->_ = $Value;
        $this->Group = $Group;
    }
}
