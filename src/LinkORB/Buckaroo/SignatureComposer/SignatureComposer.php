<?php

namespace LinkORB\Buckaroo\SignatureComposer;

/**
 * SignComposer
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
interface SignatureComposer
{
    /**
     * Compose sign string based on Buckaroo response parameters
     */
    public function compose(array $parameters) :string;
}
