<?php

namespace silentgecko\Buckaroo\SOAP;

class IPAddress
{
    // phpcs:disable
    public $_;
    // phpcs:enable
    public $Type;

    public function __construct($address, $Type = 'IPv4')
    {
        $this->_ = $address;
        $this->Type = $Type;
    }
}
