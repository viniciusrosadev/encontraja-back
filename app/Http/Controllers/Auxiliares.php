<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Carbon;

class Auxiliares extends Controller
{
    public function normalizaDataCriadoEm ($data) {
        $dataConvertida = "Criado em: " . Carbon::createFromFormat('Y-m-d H:i:s', $data)->format('d/m/Y');

        return $dataConvertida;
    }
}
