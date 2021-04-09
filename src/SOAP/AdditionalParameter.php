<?php

namespace silentgecko\Buckaroo\SOAP;

class AdditionalParameter
{
    // phpcs:disable
    public $_;
    // phpcs:enable
    public $Name;

    public function __construct($Name, $Value)
    {
        $this->Name = $Name;
        $this->_ = $Value;
    }
}
