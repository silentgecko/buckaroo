<?php

namespace LinkORB\Buckaroo\SOAP\Type;

/**
 * RequestErrors
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class RequestErrors
{
    public ChannelError $ChannelError;
    public ServiceError $ServiceError;
    public ActionError $ActionError;
    public ParameterError $ParameterError;
    public CustomParameterError $CustomParameterError;

    public function getErrors() :array
    {
        $errors = [];

        if ($this->ChannelError) {
            $errors[] = $this->ChannelError;
        }

        if ($this->ServiceError) {
            $errors[] = $this->ServiceError;
        }

        if ($this->ActionError) {
            $errors[] = $this->ActionError;
        }

        if ($this->ParameterError) {
            $errors[] = $this->ParameterError;
        }

        if ($this->CustomParameterError) {
            $errors[] = $this->CustomParameterError;
        }

        return $errors;
    }
}
