<?php

namespace AxiomePayments\Exception;

use Exception;

/**
 * Base exception class for all AxiomePayments SDK exceptions
 */
class AxiomePaymentsException extends Exception
{
    /**
     * Create a new AxiomePaymentsException instance
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 