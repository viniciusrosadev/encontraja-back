<?php

namespace EncontraJa\Http\Controllers;

use EncontraJa\Http\Controllers\LocEstados;
use EncontraJa\Http\Controllers\LocMicroRegioes;
use EncontraJa\Http\Controllers\LocCidades;

class Locais extends Controller
{
    public function populaLocais () {
        $locEstados = new LocEstados();
        $locMicroRegioes = new LocMicroRegioes();
        $locCidades = new LocCidades();

        $executaLocEstados = $locEstados->populaEstados();
        $executaLocMicroRegioes = $locMicroRegioes->populaMicroRegioes();
        $executaLocCidades = $locCidades->populaCidades();

        if ($executaLocEstados == true && $executaLocMicroRegioes == true && $executaLocCidades == true) {
            return response()->json([
                'Operacao' => 'OK'
            ]);
        } else {
            return response()->json([
                'Operacao' => 'Erro na operação de inserção'
            ]);
        }
    }
}
