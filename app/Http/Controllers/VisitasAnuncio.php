<?php

namespace EncontraJa\Http\Controllers;
use Illuminate\Support\Facades\DB;


class VisitasAnuncio extends Controller
{
    public function adicionaAcessoAoAnuncio($idAnuncio, $idUsuario) {

        if ($idUsuario == NULL) {
            $idUsuario = 0; // Visitantes 
        }

        $inserirVisitasAnuncio = DB::table('visitasAnuncio')->insert([
            "idAnuncio" => $idAnuncio,
            "idLogin" => $idUsuario
        ]);

        return $inserirVisitasAnuncio;
    }

    public function contarVisitasDoAnuncio ($idAnuncio) {
        $contarVisitas = DB::table('visitasAnuncio')->where('idAnuncio', '=', $idAnuncio)->count(['id']);

        return $contarVisitas;
    }
}
