<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LocalizacaoAnuncio extends Controller
{
    public function salvarLocalizacaoAnuncio ($idAnuncio, $idLogin, $cidadesAnuncio) {
        $colecaoLocalizacaoCAnuncio = new Collection();
        
        foreach ($cidadesAnuncio as $cidadeAnuncio) {
            $idEstado = $cidadeAnuncio['idEstado'];
            $idMicroRegiao = $cidadeAnuncio['idMicroRegiao'];
            $idCidade = $cidadeAnuncio['idCidade'];
            $cidadePrincipal = $cidadeAnuncio['cidadePrincipal'];

            $colecaoLocalizacaoCAnuncio->add([
                'idAnuncio' => $idAnuncio,
                'idLogin' => $idLogin,
                'idEstado' => $idEstado,
                'idMicroRegiao' => $idMicroRegiao,
                'idCidade' => $idCidade,
                'cidadePrincipal' => $cidadePrincipal
            ]);
        }

        $inserirLocalizacaoAnuncio = DB::table('localizacaoAnuncio')->insert($colecaoLocalizacaoCAnuncio->toArray());

        return $inserirLocalizacaoAnuncio;
    }

    public function carregarLocalizacaoAnuncio ($idAnuncioCarregado) {
        $idAnuncio = $idAnuncioCarregado;

        $pesquisaLocalizacaoAnuncio = DB::table('localizacaoAnuncio')
        ->join('locEstados', 'localizacaoAnuncio.idEstado', '=', 'locEstados.id')
        ->join('locMicroRegiao', 'localizacaoAnuncio.idMicroRegiao', '=', 'locMicroRegiao.id')
        ->join('locCidades', 'localizacaoAnuncio.idCidade', '=', 'locCidades.id')
        ->where('localizacaoAnuncio.idAnuncio', '=', $idAnuncio)
        ->select(
        'localizacaoAnuncio.cidadePrincipal',
        'locCidades.nome AS nomeCidade',
        'locMicroRegiao.nome AS nomeMicroRegiao',
        'locEstados.uf')
        ->get()->first();

        return response()->json($pesquisaLocalizacaoAnuncio);
    }
}
