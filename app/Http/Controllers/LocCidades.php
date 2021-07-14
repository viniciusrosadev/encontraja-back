<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LocCidades extends Controller
{
    public function populaCidades ()
    {
        $jsonCidades = file_get_contents(public_path('/jsonLocais/cidades.json'));
        $getCidades = json_decode($jsonCidades);

        $colecaoCidade = new Collection();

        foreach ($getCidades as $cidade) {
            $id = $cidade->id;
            $idEstado = $cidade->microrregiao->mesorregiao->UF->id;
            $idMicroRegiao = $cidade->microrregiao->id;
            $nome = $cidade->nome;

            $colecaoCidade->add([
                'id'=> $id,
                'idEstado' => $idEstado,
                'idMicroRegiao' => $idMicroRegiao,
                'nome' => $nome
            ]);
        }

        $inserircidade = DB::table('locCidades')->insert($colecaoCidade->toArray());

        return $inserircidade;
    }

    public function localizaCidades ($idE, $idM) {
        $idEstado = $idE;
        $idMicroRegiao = $idM;

            $pesquisaMicroRegioes = DB::table('locCidades')
            ->where('idEstado', '=', $idEstado)
            ->where('idMicroRegiao', '=', $idMicroRegiao)->orderBy('nome', 'asc')->get([
                'locCidades.id',
                'locCidades.nome'
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
