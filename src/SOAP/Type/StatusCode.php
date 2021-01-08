<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * StatusCode
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class StatusCode
{
    /** @var int */
    public $Code;
    /** @var string */
    public $_;

    public function getCode() :int
    {
        return $this->Code;
    }

    public function getText() :string
    {
        return $this->_;
    }
}
