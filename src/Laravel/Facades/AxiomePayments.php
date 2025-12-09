<?php

namespace AxiomePayments\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \AxiomePayments\Service\PaymentService payments()
 * @method static \AxiomePayments\Http\Client getClient()
 * @method static mixed getConfig(string $key, $default = null)
 *
 * @see \AxiomePayments\AxiomePayments
 */
class AxiomePayments extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'axiomepayments';
    }
}