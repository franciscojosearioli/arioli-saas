<?php

namespace App\Exceptions;

use RuntimeException;

class StaleRecordException extends RuntimeException
{
    public function __construct(string $model, int|string $id)
    {
        parent::__construct(
            "El registro fue modificado por otro usuario. Por favor, recargá la página y volvé a intentarlo."
        );
    }
}
