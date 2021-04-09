<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * RequiredAction
 *
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class RequiredAction
{
    public string $RedirectURL;
    public string $Type;
    public string $Name;

    public function isRedirect(): bool
    {
        return $this->Type == 'Redirect';
    }
}
