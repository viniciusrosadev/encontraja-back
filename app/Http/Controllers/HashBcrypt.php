<?php

namespace EncontraJa\Http\Controllers;

class HashBcrypt extends Controller
{
    private function montaHash($senhaDefinida, $criacaoUsuario) {
        $alfabetoComumSortido =  null;
        $senhaBase = null;

        if (is_null($senhaDefinida)) {
            $alfabetoComumSortido =  str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ456789abcdefghijklmnopqrstuvwxyz');
            $senhaBase = rand (0, $this->geradorNumeroGrad()) . $alfabetoComumSortido . $this->geradorNumeroGrad();
        } else {
            unset ($alfabetoComumSortido);
            $senhaBase = $senhaDefinida;
        }

        $profundidade = rand(4,10);

        if ($profundidade < 10) {
            $profundidade = '0' . $profundidade;
        } else {
            $profundidade = (string) $profundidade;
        }

        $salto = str_shuffle('EncontraJaFeitoNaTerra');
        
        // Gera um hash baseado em bcrypt
        $senhaMontada = crypt($senhaBase, '$2a$' . $profundidade . '$' . $salto . '$');

        if (is_null($criacaoUsuario) == false) {
            return [
                'senhaMontada' => $senhaMontada,
                'senhaBase' => $senhaBase
            ];
        } else {
            return $senhaMontada;
        }
    }

    private function verificaHash ($verificaSenha, $verificaHashUsuario) {
        if (crypt($verificaSenha, $verificaHashUsuario) === $verificaHashUsuario) {
            return true;
        } else {
            return false;
        };
    }

    private function geradorNumeroGrad () {
        $numero = deg2rad(mt_rand(0, 250));

        return $numero;
    }

    public function mostraHash($senhaDefinida = null, $criacaoUsuario = null) {
        $hash = $this->montaHash($senhaDefinida, $criacaoUsuario);

        return $hash;
    }

    public function checaHash($senha, $hashUsuario) {
        if (!empty($senha)) {
            $statusRetorno = $this->verificaHash($senha, $hashUsuario);

            if ($statusRetorno == true) {
                return true;
            } else {
                return false;
            }
        }
    }
}