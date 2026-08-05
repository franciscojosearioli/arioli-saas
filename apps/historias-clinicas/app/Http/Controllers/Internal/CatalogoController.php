<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Platform\CatalogoSerializer;
use Illuminate\Http\JsonResponse;

/**
 * Etapa 7.2 (ver docs/CATALOGO_COMPONENTES.md): adaptador puro — no lee
 * `config()` directamente, no arma ningún array acá. El catálogo
 * (`config/platform/componentes.php` + `perfiles.php`) sigue siendo la
 * única fuente; esto solo lo serializa para quien no puede leer el
 * filesystem de historias-clinicas (la app central, hoy).
 */
class CatalogoController extends Controller
{
    public function __construct(private readonly CatalogoSerializer $serializer)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->serializer->catalogoCompleto());
    }
}
