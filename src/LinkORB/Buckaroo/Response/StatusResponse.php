<?php

namespace LinkORB\Buckaroo\Response;

use DateTime;

/**
 * StatusResponse.
 *
 * @see PostResponse
 * @author  Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class StatusResponse extends PostResponse
{
    const PENDING_INPUT = 790;
    const PENDING_PROCESSING = 791;
    const AWAITING_CUSTOMER = 792;
    const SUCCESS = 190;
    const FAILED = 490;
    const VALIDATION_FAILURE = 491;
    const TECHNICAL_FAILURE = 492;
    const CANCELLED_BY_USER = 890;
    const CANCELLED_BY_MERCHANT = 891;
    const REJECTED = 690;

    public function getTransactionKey() :string
    {
        return $this->getParameter('brq_transactions');
    }

    public function isTest() :bool
    {
        return $this->hasParameter('brq_test') && $this->getParameter('brq_test') === 'true';
    }

    public function getTimestamp() : DateTime
    {
        return new DateTime($this->getParameter('brq_timestamp'));
    }

    public function getInvoiceNumber() :string
    {
        return $this->getPayment('brq_invoicenumber');
    }

    public function getPayment() :string
    {
        return $this->getParameter('brq_payment');
    }

    public function isSuccess() :bool
    {
        return $this->getStatusCode() == static::SUCCESS;
    }

    public function getStatusCode() :int
    {
        return (int)$this->getParameter('brq_statuscode');
    }

    public function isFinal() :bool
    {
        return !$this->isPending();
    }

    public function isPending() :bool
    {
        return in_array(
            $this->getStatusCode(),
            [static::PENDING_INPUT, static::PENDING_PROCESSING, static::AWAITING_CUSTOMER]
        );
    }

    public function isCancelled() :bool
    {
        return in_array(
            $this->getStatusCode(),
            [static::CANCELLED_BY_MERCHANT, static::CANCELLED_BY_USER]
        );
    }

    public function isFailed() :bool
    {
        return in_array(
            $this->getStatusCode(),
            [static::FAILED, static::TECHNICAL_FAILURE, static::VALIDATION_FAILURE]
        );
    }
}
