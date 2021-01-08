<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * Body
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class Body
{
    /** @var string */
    public $Key;
    /** @var Status */
    public $Status;
    /** @var RequiredAction */
    public $RequiredAction;

    /**
     * Order number
     * @var string
     */
    public $Invoice;

    /**
     * Whether this is a test transaction
     * @var bool
     */
    public $IsTest;
    /** @var string */
    public $Currency;
    /** @var float */
    public $AmountDebit;

    /**
     * One of: NotSet, Collecting, Processing, Informational
     * @var string
     */
    public $MutationType;
    /** @var bool */
    public $StartRecurrent;
    /** @var bool */
    public $Recurring;
    /** @var RequestErrors */
    public $RequestErrors;

    public function hasRequiredAction() :bool
    {
        return $this->RequiredAction !== null;
    }

    public function hasErrors() :bool
    {
        return $this->RequestErrors !== null;
    }
}
