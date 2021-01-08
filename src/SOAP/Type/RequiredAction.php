<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * RequiredAction
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class RequiredAction
{
    /** @var string */
    public $RedirectURL;
    /** @var string */
    public $Type;
    /** @var string */
    public $Name;

    public function isRedirect() :bool
    {
        return $this->Type == 'Redirect';
    }
}
