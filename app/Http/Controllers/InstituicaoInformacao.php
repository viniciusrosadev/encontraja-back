<?php

namespace EncontraJa\Http\Controllers;

use EncontraJa\Http\Requests\InstituicaoInformacaoRequest;
use Illuminate\Support\Facades\DB;

class InstituicaoInformacao extends Controller
{
    public function cadastraInstituicao (InstituicaoInformacaoRequest $request) {
        $dadosInstituicao = $request->all();

        $idLogin = $dadosInstituicao['idLogin'];
        $nomeInstituicao = $dadosInstituicao['nomeInstituicao'];
        $dataAbertura = $dadosInstituicao['dataAbertura'];
        $dataFechado = $dadosInstituicao['dataFechado'];
        $cnpj = $dadosInstituicao['cnpj'];
        $tipo = $dadosInstituicao['tipo'];
        $descricao = $dadosInstituicao['descricao'];

        $inserirInstituicao = DB::table('instituicaoInformacao')->insert([
            'idLogin' => $idLogin,
            'nomeInstituicao' => $nomeInstituicao,
            'dataAbertura' => $dataAbertura,
            'dataFechado' => $dataFechado,
            'cnpj' => $cnpj,
            'tipo' => $tipo,
            'descricao' => $descricao
        ]);

        return response()->json([
            'status' => 200,
            'transacao' => 'OK'
        ]);
    }
}
