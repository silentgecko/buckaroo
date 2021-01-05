<?php

namespace LinkORB\Buckaroo\SOAP\Type;

/**
 * ParameterError
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class ParameterError extends Error
{
    public string $Action;
    public string $Service;
}
