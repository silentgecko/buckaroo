<?php

namespace silentgecko\Buckaroo\Response;

use ArrayAccess;
use InvalidArgumentException;
use RuntimeException;
use silentgecko\Buckaroo\SignatureComposer\SignatureComposer;

/**
 * PostResponse can be used to verify and read post and push responses from Buckaroo.
 *
 * <code>
 * use silentgecko\Buckaroo\Response\PostResponse;
 * use silentgecko\Buckaroo\SignatureComposer\Sha1Composer;
 *
 * $response = new PostResponse($_POST);
 * if ($response->isValid(new Sha1Composer('YourSecretKey')) {
 *     var_dump($response->getParameter('BRQ_STATUSCODE'));
 * }
 * </code>
 *
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class PostResponse implements ArrayAccess
{
    protected const SIGNATURE_FIELD = 'BRQ_SIGNATURE';
    protected array $parameters;
    protected string $signature;
    protected array $upperParameters;

    public function __construct(array $parameters)
    {
        $upperParameters = array_change_key_case($parameters, CASE_UPPER);
        $this->signature = $this->getSignature($upperParameters);
        unset($parameters[static::SIGNATURE_FIELD], $parameters[strtolower(static::SIGNATURE_FIELD)]);

        $this->parameters = $parameters;
        $this->upperParameters = array_change_key_case($parameters, CASE_UPPER);
    }

    /**
     * Extract the sign field
     *
     * @throws InvalidArgumentException
     */
    protected function getSignature(array $parameters): string
    {
        if (!array_key_exists(static::SIGNATURE_FIELD, $parameters) || $parameters[static::SIGNATURE_FIELD] == '') {
            throw new InvalidArgumentException(
                sprintf('Sign key (%s) not present in parameters.', static::SIGNATURE_FIELD)
            );
        }

        return $parameters[static::SIGNATURE_FIELD];
    }

    /**
     * Returns whether this response is valid
     */
    public function isValid(SignatureComposer $composer): bool
    {
        // Constant Time String Comparison @see http://php.net/hash_equals
        return hash_equals($composer->compose($this->parameters), $this->signature);
    }

    /**
     * Returns whether the parameter exists
     */
    public function hasParameter(string $key): bool
    {
        return isset($this->upperParameters[strtoupper($key)]);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->upperParameters[strtoupper($offset)]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): string
    {
        return $this->getParameter($offset);
    }

    /**
     * Returns the value for the given key
     *
     * @throws InvalidArgumentException
     */
    public function getParameter(string $key): string
    {
        $key = strtoupper($key);

        if (!isset($this->upperParameters[$key])) {
            throw new InvalidArgumentException('Parameter ' . $key . ' does not exist.');
        }

        return $this->upperParameters[$key];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('It is not possible to change the parameters.');
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('It is not possible to change the parameters.');
    }
}
