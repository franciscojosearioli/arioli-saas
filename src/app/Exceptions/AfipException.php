<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Errores reales devueltos por los web services de AFIP (WSAA/WSFEv1) — el
 * mensaje siempre es el que devuelve AFIP tal cual, nunca se reemplaza por
 * uno genérico: es un sistema fiscal, el admin necesita el motivo exacto.
 */
class AfipException extends RuntimeException
{
}
