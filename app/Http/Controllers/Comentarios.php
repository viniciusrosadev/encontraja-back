<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Facades\DB;
use EncontraJa\Http\Requests\ComentariosRequest;

class Comentarios extends Controller
{
    public function guardarComentario (ComentariosRequest $request) {
        $dadosComentario = $request->all();

        $idLogin = $dadosComentario['idLogin']        ;
        $idPessoaDestinada = $dadosComentario['idPessoaDestinada'];
        $comentario = $dadosComentario['comentario'];
        $recomenda = $dadosComentario['recomenda'];

        $inserirComentario = DB::table('comentarios')->insert([
            'idLogin' => $idLogin,
            'idPessoaDestinada' => $idPessoaDestinada,
            'comentario' => $comentario,
            'recomenda' => $recomenda
        ]);

        return response()->json([
            'status' => 200,
            'transacao' => 'OK'
        ]);
    }
}
