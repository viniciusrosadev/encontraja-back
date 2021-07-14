<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Facades\DB;

class Termo extends Controller
{
    public function salvarTermo ($idLogin, $tipo, $statusTermo) {

        switch ($statusTermo) {
            case true:
                $statusTermo = 1;
                break;

            default:
                $statusTermo = 0;
                break;
        }

        $executaTermo = DB::table('termo')->insert([
            'idLogin' =>$idLogin,
            'tipo' => $tipo,
            'statusTermo' => $statusTermo
        ]);
    }
}
