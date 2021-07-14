<?php

namespace EncontraJa\Http\Controllers;

use EncontraJa\Http\Requests\ClassificacaoUsuarioRequest;
use Illuminate\Support\Facades\DB;

class ClassificacaoUsuario extends Controller
{
    public function guardarClassificacao (ClassificacaoUsuarioRequest $request) {
        $dadosClassificacao = $request->all();

        $idLogin = $dadosClassificacao['idLogin'];
        $idPessoaAcessada = $dadosClassificacao['idPessoaAcessada'];
        $nota = $dadosClassificacao['nota'];

        $inserirClassificacao = DB::table('classificacaoUsuario')->insert([
            'idLogin' => $idLogin,
            'idPessoaAcessada' => $idPessoaAcessada,
            'nota' => $nota
        ]);

        return response()->json([
            'status' => 200,
            'transacao' => 'OK'
        ]);
    }
}
