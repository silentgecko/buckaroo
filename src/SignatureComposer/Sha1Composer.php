<?php

namespace silentgecko\Buckaroo\SignatureComposer;

/**
 * Sha1Composer, composes a sha1 sign for a Buckaroo post/push response.
 *
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class Sha1Composer implements SignatureComposer
{
    protected string $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function compose(array $parameters) :string
    {
        return $this->sign($this->sort($parameters));
    }

    /**
     * Calculate the sha1 for the parameter array
     */
    protected function sign(array $parameters) :string
    {
        //turn into string and add the secret key to the end
        $signatureString = '';

        foreach ($parameters as $key => $value) {
            $signatureString .= $key . '=' . $value;
        }

        $signatureString .= $this->secret;

        return sha1($signatureString);
    }

    /**
     * Sort array alphabetically on key
     */
    protected function sort(array $parameters) :array
    {
        uksort(
            $parameters,
            function ($key1, $key2) {
                return strtolower($key1) > strtolower($key2) ? 1 : -1;
            }
        );

        return $parameters;
    }
}
