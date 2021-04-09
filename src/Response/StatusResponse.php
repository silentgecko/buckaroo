<?php

namespace silentgecko\Buckaroo\Response;

use DateTime;

/**
 * StatusResponse.
 *
 * @see     PostResponse
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class StatusResponse extends PostResponse
{
    protected const PENDING_INPUT         = 790;
    protected const PENDING_PROCESSING    = 791;
    protected const AWAITING_CUSTOMER     = 792;
    protected const SUCCESS               = 190;
    protected const FAILED                = 490;
    protected const VALIDATION_FAILURE    = 491;
    protected const TECHNICAL_FAILURE     = 492;
    protected const CANCELLED_BY_USER     = 890;
    protected const CANCELLED_BY_MERCHANT = 891;
    protected const REJECTED              = 690;

    public function getTransactionKey(): string
    {
        return $this->getParameter('brq_transactions');
    }

    public function isTest(): bool
    {
        return $this->hasParameter('brq_test') && $this->getParameter('brq_test') === 'true';
    }

    public function getTimestamp(): DateTime
    {
        return new DateTime($this->getParameter('brq_timestamp'));
    }

    public function getInvoiceNumber(): string
    {
        return $this->getPayment('brq_invoicenumber');
    }

    public function getPayment(): string
    {
        return $this->getParameter('brq_payment');
    }

    public function isSuccess(): bool
    {
        return $this->getStatusCode() == static::SUCCESS;
    }

    public function getStatusCode(): int
    {
        return (int)$this->getParameter('brq_statuscode');
    }

    public function isFinal(): bool
    {
        return !$this->isPending();
    }

    public function isPending(): bool
    {
        return in_array(
            $this->getStatusCode(),
            [static::PENDING_INPUT, static::PENDING_PROCESSING, static::AWAITING_CUSTOMER]
        );
    }

    public function isCancelled(): bool
    {
        return in_array(
            $this->getStatusCode(),
            [static::CANCELLED_BY_MERCHANT, static::CANCELLED_BY_USER]
        );
    }

    public function isFailed(): bool
    {
        return in_array(
            $this->getStatusCode(),
            [static::FAILED, static::TECHNICAL_FAILURE, static::VALIDATION_FAILURE]
        );
    }
}
