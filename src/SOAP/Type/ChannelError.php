<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * ChannelError
 *
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class ChannelError extends Error
{
    public string $Service;
    public string $Action;
}
