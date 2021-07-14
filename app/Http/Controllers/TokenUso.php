<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TokenUso extends Controller
{
    public function tokenIdentificaIdUsuario ($token) {
        $pesquisaIdUsuario = DB::table('tokenUsuario')->where('token', '=', $token)->get('idUsuario')->first();

        if (!$pesquisaIdUsuario) {
            return '';
        } else {
            return $pesquisaIdUsuario->idUsuario;
        }
    }

    public function invalidarToken ($idUsuario) {
        $deletarToken = DB::table('tokenUsuario')->where('idUsuario', '=', $idUsuario)->delete();

        return $deletarToken;
    }
}
