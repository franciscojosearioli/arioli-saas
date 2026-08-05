<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Se lanza cuando se invoca una operación de un driver (pagos, facturación,
 * firma) cuya integración real todavía no está implementada o configurada.
 */
class IntegrationNotReadyException extends RuntimeException
{
}
