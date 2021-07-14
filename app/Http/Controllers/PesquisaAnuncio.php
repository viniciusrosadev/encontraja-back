<?php

namespace EncontraJa\Http\Controllers;

use EncontraJa\Http\Requests\PesquisaAnuncioRequest;
use Illuminate\Support\Facades\DB;
use EncontraJa\Http\Controllers\TokenUso;
use EncontraJa\Http\Controllers\AnuncioServico;
use Illuminate\Database\Eloquent\Collection;

class PesquisaAnuncio extends Controller
{
/*
    A pesquisa deve conter um total de 6 anúncios:
    - (2 sem data informada e com data informada 4) mais visitados e recentes de acordo com a data informada
    - 2 criados aleatoriamente
    - 2 menos acessados
*/
    public function pesquisaAnuncio(PesquisaAnuncioRequest $request) {
        $operacaoRequest = $request->all();

        $token = $operacaoRequest['token'];

        $procuraToken = new TokenUso();
        $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            $idUsuario = null;
        }

        if (isset($operacaoRequest['cidadePesquisada'])) {
        $cidadePesquisada = $operacaoRequest['cidadePesquisada'];
        } else {
        $cidadePesquisada = null;
        }

        if (isset($operacaoRequest['descricaoPesquisada'])) {
        $descricaoPesquisada = $operacaoRequest['descricaoPesquisada'];
        } else {
        $descricaoPesquisada = null;
        }

        if (isset($operacaoRequest['dataInicialPesquisada'])) {
        $dataInicialPesquisada = $operacaoRequest['dataInicialPesquisada'];
        } else {
        $dataInicialPesquisada = null;
        }

        if (isset($operacaoRequest['dataFinalPesquisada'])) {
        $dataFinalPesquisada = $operacaoRequest['dataFinalPesquisada'];    
        } else {
        $dataFinalPesquisada = null;
        }
        
        $termoPesquisado = $operacaoRequest['termoPesquisado'];
        
        $procuraAnuncio = new AnuncioServico();
        $quantidadeAnuncios = 2;

        if (isset($dataInicialPesquisada) || isset($dataFinalPesquisada)) {
            $quantidadeAnuncios = 4;
        }

        $idsAnunciosExistentes = new Collection();
        $pesquisaEnvio = [];
        $pesquisaAnunciosAleatorios = null;

        $pesquisaAnunciosMaisVisitadosERecentes = $procuraAnuncio->anunciosMaisVisitadosERecentes($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada, $quantidadeAnuncios);
        $pesquisaAnunciosMaisVisitadosERecentes->map(function($item) use ($idsAnunciosExistentes){
            $idsAnunciosExistentes->add($item['idAnuncio']);
        });

        foreach ($pesquisaAnunciosMaisVisitadosERecentes->toArray() as $anuncio) {
            $pesquisaEnvio[] = $anuncio;
        }

        if ($quantidadeAnuncios == 2) {
            $pesquisaAnunciosAleatorios = $procuraAnuncio->anunciosAleatorios($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada, $idsAnunciosExistentes->toArray());
            $pesquisaAnunciosAleatorios->map(function($item) use ($idsAnunciosExistentes){
                $idsAnunciosExistentes->add($item['idAnuncio']);
            });

            foreach ($pesquisaAnunciosAleatorios->toArray() as $anuncio) {
                $pesquisaEnvio[] = $anuncio;
            }
        }

        $pesquisaAnunciosMenosVisitados = $procuraAnuncio->AnunciosMenosVisitados($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada, $idsAnunciosExistentes->toArray());
        
        foreach ($pesquisaAnunciosMenosVisitados->toArray() as $anuncio) {
            $pesquisaEnvio[] = $anuncio;
        }

        $guardarPesquisaAnuncio = $this->guardarPesquisaAnuncio($idUsuario, $cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada);

        if ($guardarPesquisaAnuncio == false) {
            return response()->json([
                "resultado" => "Nenhum anúncio encontrado."
            ]);
        } else {
            return response()->json($pesquisaEnvio);
        }
    }

    public function guardarPesquisaAnuncio ($idLogin, $cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada) {
        $inserirPesquisaAnuncio = DB::table('pesquisaAnuncio')->insert([
            'cidadePesquisada' => $cidadePesquisada,
            'termoPesquisado' => $termoPesquisado,
            'descricaoPesquisada' => $descricaoPesquisada,
            'dataInicialPesquisada' => $dataInicialPesquisada,
            'dataFinalPesquisada' => $dataFinalPesquisada
        ]);

        if ($inserirPesquisaAnuncio == true) {
            return true;
        } else {
            return false;
        }
    }

}
