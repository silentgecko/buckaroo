<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * Error
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
abstract class Error
{
    /** @var string */
    public $Name;
    /** @var string */
    public $Error;
    /** @var string */
    public $_;

    public function __toString() :string
    {
        return (string)$this->getMessage();
    }

    /**
     * Returns the error message
     */
    public function getMessage() :string
    {
        return $this->_;
    }
}
