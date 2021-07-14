<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use EncontraJa\Http\Controllers\TokenUso;

class LocMicroRegioes extends Controller
{
    public function populaMicroRegioes () {
        $jsonMicroRegioes = file_get_contents(public_path('/jsonLocais/microregioes.json'));
        $getMicroRegioes = json_decode($jsonMicroRegioes);

        $colecaoMicroRegioes = new Collection();

        foreach ($getMicroRegioes as $microregioes) {
            $id = $microregioes->id;
            $idEstado = $microregioes->mesorregiao->UF->id;
            $nome = $microregioes->nome;

            $colecaoMicroRegioes->add([
                'id' => $id,
                'idEstado' => $idEstado,
                'nome' => $nome
            ]);
        }

        $inserirMicroRegioes = DB::table('locMicroRegiao')->insert($colecaoMicroRegioes->toArray());

        return $inserirMicroRegioes;
    }

    public function localizaMicroRegioes ($id) {
        $idEstado = $id;

            $pesquisaMicroRegioes = DB::table('locMicroRegiao')->where('idEstado', '=', $idEstado)->orderBy('nome', 'asc')->get([
                'locMicroRegiao.id',
                'locMicroRegiao.nome'
            ]);

            if ($pesquisaMicroRegioes == true) {
                return response()->json($pesquisaMicroRegioes);
            } else {
                return response()->json([
                    'erro' => 'Erro durante localização'
                ]);
            }
        }
}
