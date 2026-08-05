<?php

/*
 * Catálogo de piezas dentales — Etapa 6.6.5 (ver docs/ARQUITECTURA_MODULAR.md).
 * Dato estático puro, no tabla: nunca cambia, no es dato de tenant, mismo
 * criterio que Perfil/Componente ("aburrido a propósito"). Reemplaza a
 * Odontograma::numerosFdiAdulto(), que solo sabía generar los 32 números
 * permanentes y no sabía nada de nombre anatómico, tipo de diente ni qué
 * superficies le aplican a cada uno (un incisivo no tiene cara oclusal,
 * tiene incisal en su lugar).
 *
 * Notación FDI: permanentes 11-18/21-28/31-38/41-48 (cuadrantes 1-4);
 * temporales (dentición de leche) 51-55/61-65/71-75/81-85 (cuadrantes
 * 5-8) — misma lógica de numeración, cuadrante+posición, familia FDI
 * completa. Dentición temporal solo tiene 5 piezas por cuadrante (no hay
 * premolares de leche más allá del "molar temporal"), por eso el array de
 * posiciones es distinto por dentición.
 *
 * Closure local en vez de función con nombre a propósito: un archivo de
 * config puede volver a cargarse en el mismo proceso (mergeConfigFrom no
 * usa require_once) — una función con nombre en scope global rompería con
 * "Cannot redeclare" la segunda vez; un closure en variable local no.
 */
$piezaAnatomica = function (int $cuadrante, int $posicion, string $denticion): array {
    $esSuperior = in_array($cuadrante, [1, 2, 5, 6], true);
    $esDerecha = in_array($cuadrante, [1, 4, 5, 8], true);

    $nombresPermanentes = [
        1 => 'Incisivo Central', 2 => 'Incisivo Lateral', 3 => 'Canino',
        4 => 'Primer Premolar', 5 => 'Segundo Premolar',
        6 => 'Primer Molar', 7 => 'Segundo Molar', 8 => 'Tercer Molar',
    ];
    $nombresTemporales = [
        1 => 'Incisivo Central', 2 => 'Incisivo Lateral', 3 => 'Canino',
        4 => 'Primer Molar', 5 => 'Segundo Molar',
    ];

    $tiposPermanentes = [1 => 'incisivo', 2 => 'incisivo', 3 => 'canino', 4 => 'premolar', 5 => 'premolar', 6 => 'molar', 7 => 'molar', 8 => 'molar'];
    $tiposTemporales = [1 => 'incisivo', 2 => 'incisivo', 3 => 'canino', 4 => 'molar', 5 => 'molar'];

    $nombre = $denticion === 'permanente' ? $nombresPermanentes[$posicion] : $nombresTemporales[$posicion];
    $tipo = $denticion === 'permanente' ? $tiposPermanentes[$posicion] : $tiposTemporales[$posicion];

    // Incisivos y caninos usan cara "incisal" (el borde de corte); premolares
    // y molares usan "oclusal" (la cara masticatoria) — nunca ambas a la vez.
    $superficies = in_array($tipo, ['incisivo', 'canino'], true)
        ? ['incisal', 'vestibular', 'palatina_lingual', 'mesial', 'distal']
        : ['oclusal', 'vestibular', 'palatina_lingual', 'mesial', 'distal'];

    return [
        'nombre' => $nombre . ' ' . ($esSuperior ? 'Superior' : 'Inferior') . ' ' . ($esDerecha ? 'Derecho' : 'Izquierdo'),
        'tipo' => $tipo,
        'denticion' => $denticion,
        'cuadrante' => $cuadrante,
        'superficies' => $superficies,
    ];
};

$catalogo = [];

foreach ([1, 2, 3, 4] as $cuadrante) {
    foreach (range(1, 8) as $posicion) {
        $catalogo[($cuadrante * 10) + $posicion] = $piezaAnatomica($cuadrante, $posicion, 'permanente');
    }
}

foreach ([5, 6, 7, 8] as $cuadrante) {
    foreach (range(1, 5) as $posicion) {
        $catalogo[($cuadrante * 10) + $posicion] = $piezaAnatomica($cuadrante, $posicion, 'temporal');
    }
}

// Anidado bajo una clave string a propósito: PlatformServiceProvider
// registra este archivo con mergeConfigFrom(), que internamente hace
// array_merge($esteArray, $configExistente) — array_merge RENUMERA
// cualquier clave puramente entera del array de nivel superior (11, 12...
// pasan a ser 0, 1...), incluso mezclando contra un array vacío. Confirmado
// empíricamente antes de este fix, no asumido. Anidar el catálogo bajo
// 'piezas' hace que array_merge solo vea UNA clave string en el nivel
// superior — nunca toca el array interno (array_merge no es recursivo),
// así que los números FDI quedan intactos como claves.
return ['piezas' => $catalogo];

return $catalogo;
