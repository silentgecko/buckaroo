<?php

namespace LinkORB\Buckaroo\SOAP\Type;

/**
 * Body
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class Body
{
    public string $Key;
    public Status $Status;
    public RequiredAction $RequiredAction;

    /**
     * Order number
     */
    public string $Invoice;

    /**
     * Whether this is a test transaction
     */
    public bool $IsTest;
    public string $Currency;
    public float $AmountDebit;

    /**
     * One of: NotSet, Collecting, Processing, Informational
     */
    public string $MutationType;
    public bool $StartRecurrent;
    public bool $Recurring;
    public RequestErrors $RequestErrors;

    public function hasRequiredAction() :bool
    {
        return $this->RequiredAction !== null;
    }

    public function hasErrors() :bool
    {
        return $this->RequestErrors !== null;
    }
}
