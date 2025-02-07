<?php

namespace silentgecko\Buckaroo\SOAP\Type;

/**
 * StatusCode
 *
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class StatusCode
{
    public int $Code;
    // phpcs:disable
    public string $_;
    // phpcs:enable

    public function getCode(): int
    {
        return $this->Code;
    }

    public function getText(): string
    {
        return $this->_;
    }
}
