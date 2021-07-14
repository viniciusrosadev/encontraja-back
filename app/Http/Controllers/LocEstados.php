<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use EncontraJa\Http\Controllers\TokenUso;

use function GuzzleHttp\json_decode;

class LocEstados extends Controller
{
    public function populaEstados(){
    $jsonEstados = file_get_contents(public_path('/jsonLocais/estados.json'));
    $getEstados = json_decode($jsonEstados);

    $colecaoEstados = new Collection();

    foreach ($getEstados as $estado) {
        $id = $estado->id;
        $uf = $estado->sigla;
        $nome = $estado->nome;

        $colecaoEstados->add([
            'id' => $id,
            'uf' => $uf,
            'nome' => $nome
        ]);
    }

    $inserirEstados = DB::table('locEstados')->insert($colecaoEstados->toArray());

    return $inserirEstados;
    }

    public function localizaEstados () {
            $pesquisaEstados = DB::table('locEstados')->orderBy('UF', 'asc')->get([
                'locEstados.id',
                'locEstados.UF'
            ]);

            if ($pesquisaEstados == true) {
                return response()->json($pesquisaEstados);
            } else {
                return response()->json([
                    'erro' => 'Erro durante localização'
                ]);
      }
    }
}
