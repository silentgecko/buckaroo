<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * ActionError
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class ActionError extends Error
{
    public string $Service;
}
