<?php

namespace EncontraJa\Http\Controllers;

use EncontraJa\Http\Requests\ImagemRequest;
use EncontraJa\Http\Controllers\TokenUso;
use EncontraJa\Http\Controllers\AnuncioServico;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Imagem extends Controller
{
    public function guardarImagem(ImagemRequest $request) {
        $operacaoRequest = $request->all();

        $pesquisaToken = new TokenUso();
        $token = $operacaoRequest['token'];

        $idUsuario = $pesquisaToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado'
            ]);
        } else {
            if(isset($operacaoRequest['idAnuncio'])) {
                $idAnuncio = $operacaoRequest['idAnuncio'];
            } else {
                $idAnuncio = NULL;
            }

            $ativo = 1;
            $imagens = $operacaoRequest['imagem'];
            $colecaoImagens = new Collection();

            foreach ($imagens as $imagem) {
                $tipo = $imagem['tipo'];
                $base64 = $imagem['base64'];

                switch ($tipo) {
                    case 'perfil':
                        $tipo = 1;
                        break;
                    case 'anuncio':
                        $tipo = 2;
                        break;
                    default:
                        $tipo = 3;
                        break;
                }

                $colecaoImagens->add([
                    "idLogin" => $idUsuario,
                    'idAnuncio' => $idAnuncio,
                    "ativo" => $ativo,
                    "tipo" => $tipo,
                    "base64" => $base64
                ]);
            }

            $inserirImagem = DB::table('imagem')->insert($colecaoImagens->toArray());

            if ($inserirImagem == true) {
                return response()->json([
                    'Transacao' => 'OK',
                    'Status' => 200
                ]);
            } else {
                return response()->json([
                    'erro' => 'Erro na inserção da imagem'
                ]);
            }
        }
    }

    public function carregarImagemAnuncio($idAnuncioCarregado) {
            $pesquisaAnuncio = new AnuncioServico();
            $auxIdAnuncio = $idAnuncioCarregado;
            $idAnuncio = $pesquisaAnuncio->validaIdAnuncio($auxIdAnuncio);

            if ($idAnuncio != false) {
                $pesquisaImagem = DB::table('imagem')
                ->where('idAnuncio', '=', $idAnuncio)->get([
                    'ativo',
                    'tipo',
                    'base64',
                    'uploadEm'
                ]);

                return response()->json($pesquisaImagem);
            } else {
                return response()->json([
                    'erro' => 'Anuncio não encontrado'
                ]);
            }
    }

    public function carregarImagemAmostragem($idAnuncioCarregado) {
        $pesquisaAnuncio = new AnuncioServico();
        $auxIdAnuncio = $idAnuncioCarregado;
        $idAnuncio = $pesquisaAnuncio->validaIdAnuncio($auxIdAnuncio);

        if ($idAnuncio != false) {
            $imagemNaoEncontrada = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAIAAAAiOjnJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAeLSURBVHhe7d3teaM8EIXh1OWCXI+rcTMUkxUgwQgNaMj62OTKc//aiEFfnBdvsL3v1zcgQLAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgQbAgEQvWMDzvt9vta3G73Z/DkI9+wPBYJ3N7fHAi2BEIlr2ItZSuXPNuBOvqusF63vP1830oWgTr6nrB2uaqekEcfeayEqyr6wSrehlc7k5+6zsRrKvrBMvesEyC7HUlWHAQLEi8LFjbSz087nPD7d5c+OnhxXQwmZ5c5APGcQ3BujpNsO45VIW9q6XA5FbLVgRqCNbVSYLVWi6+7bC2dh+oIVhXJwpWeu0yr2RJrrL93R7Ppz2p9B+pIVhXJ/s7ll+19tfWlIBEapzRcCkfC9Z+HiI1BOvq3hysqsNU6L6XHaghWFf37mBVjYvN04R+jTMaLuXtwUrGR1S52ajz0akhWFf3iWDNhmd5hFqcqhlWuQVX8rNgOc2ngzVLd6ZcsX/ridTgYjrBstkwdwunNRQsk5Cls7VtPi1Sg8vrBKu6N412P49VhW3vAWlVND78rJ5+zl1FalLR+FHpWfteJD6vF6wmWRvLTaUO1pYfv1rp6mwNN7Er6gbr6ELfllhtLvXxm9C5sWZL+jUE6+oCwRpNv56Za9k+tdxe6uVxQe9jM/MT0HxkdVxDsK4uGKw+LjUsggUJggUJggUJggUJggWJ1wXr+bgXD+fxAf6WlwULsAgWJAgWJAgWJAgWJAgWJAgWJAgWJDrBWj9xV30Qz7T/91N2+8i+HgRa0p0PB2vvI54E69e6SLCqsQmWhNnuN7zfepVg2cEJlsQfDZYZnWBJ/NVgrcMTLIk/G6xlfIIl8XeDVSawG6zmW1vtZ7PGknx4/E7X0fICvbm659lBxxVs5lSZ/w9V89GR93W15utxqcTURIZrtnoy7cj29LK+aru6a55L8uHjne8sJ+JssPIM/GDZmRpV9rY198fe8iK9eZbdq+3Oovl6rZ3GziRCNWtJZLhwsNZ/csAfwPjRzveXE3E6WHP/XrD2JpSsc/K3rwgsLzlc4cEAZpsPeh+VEQ7LctFRTaifXBYMlhXovpSEdz6ynIh4sG7l3jhen7V9uVrVjNJ/ltXLh78FU1X+82Sdeag3h92+8dXA+xdFks08NnVLYXUxmqppGpuJmvtJUvqJDDc86kWOi77P3yKvT1/lfQjt1WYKY1X+88Qt21tORDxY92f5IfXvBKsqnVrMJL0dzlX24pXlBXtrOf07TXVj7u2HTW33bUuw72rVS1tia5MUzPVY8sqdb8ucEyPOBGv56fZ4LO12AzZiy4tO3OmtFe3fNpbe2qsaqWq778xhfzi/LbGnH+5RYU4o/XRmVXpt27yqgFPBWn5cXhbrDUjq300WJ5dXdHpr2c6maZqZJkv/tq701l5Vr2qrnb6zoNhwoWDtzeNFO9+2eVUB54Jlf87MQu0UNk4ubxTorXVw0mjp39aV3n5xsGzBRqnvzKrsTNvmVQWcDFabrGWhB4s7vbxgb63D00z/tq709muDZQ83Sn1nVmVn2javKuBssJpVeBPPf7s0TSeXF+yt5XbmqPufe/utwaqP/ufOt21eVcDpYFUDJc4+OZUnlxfsreV1Nv4eny3n2TpnBbkpUtWO6MwhNpzflninr2J71ZmVc+b+ciLOB6sayd0nZ5IHyzMnrmPEems5/TtNdaOzgtzkVDVNbffOgLHhdvbCP30V2ytnVt5obZlzYsQPglUN5e3TV7ozDEPkSWG6idif1zFivTnsicEHpM4KvEWlRu8BaTOiN2BwuHq81D6+SZeavdNX9qTX7vy7HpDmtp2FbjalFqsyY8R6cxycaE4LXun6amzkyR7VlPUEh3NmP/Xg7vcqtleHVevOR5YT8aNg2faDTTHPkNaq7cT33gqN9ebZ2cHqpPCV3t1nM9W9mrUkPFwz+akP73QrtlfbaV7lTei63+WAXeh4E86TSnfRwcywrip34eMPb8R6c9UPC52PkNhBS2/+lZ7mUT16nD5Eko8Vm5ptyZnhUrF5lQoFaxr/hTvfW05EJ1jAzxAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsSBAsCHx//wMncea0Rf5SqAAAAABJRU5ErkJggg==';
            $pesquisaImagem = DB::table('imagem')
            ->where('idAnuncio', '=', $idAnuncio)
            ->where('ativo', '=', 1)->get(['base64'])->first();
            if($pesquisaImagem == NULL) {
                return $imagemNaoEncontrada;
            } else {
                return $pesquisaImagem->base64;
            }
        } else {
            return false;
        }
    }

    public function carregarImagemDePerfil($idUsuario) {
        $imagemDePerfilPadrao = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAIAAAAiOjnJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAR8SURBVHhe7d3Ncts4EIXRZObF/eYapaRiPP6RKApXRjfO2diLbIj+3KAdp/L7dDr9gtH+uX6EoYRFhLCIEBYRwiJCWEQIiwhhESEsIoRFhLCIEBYRwiJCWEQIiwhhESEsIoRFhLCIEBYRwiJCWEQIiwhhESEsIoRFhH9i/623t7frZ/fs/5PrENZfo/rQ2dnqYaUjWDayRcN6/bxXK2y5sH52wOvktUpYs020fWH9w5p5hI3zav5zrMkn1zisthur1sz6FdZzY5WbU7+wum2s6hNqU1irjdVgKsKaTpuR9HiQJmG1qeqiweOUf8dqltQHdZ+u9sbqXdWZsH5A+6ouij5m1bAWqeqi4sP2+a6wt3JtlQxrqXVVVL2wlq2q1oMXC2vxXVXo8SuF5QY8q3IIXt6JKBOWdbUpcRQ2Vknzt1UjLOuqnAJhqepLkx+Lq5CI2cOyrm6Y+XBsLCKmDsu6umvaI7KxiBAWEfOG5R7cac6DsrGImDQs6+ohEx6XjUWEsIgQFhEzhuUF64DZDs3GIkJYRAiLCGERIaw+pnp/ny4s3xL2YGMRISwihEWEsIgQFhHCIkJYRAiLCGERISwihEWEsIiYLix/CX2Y326gP2ERISwihEXEjGF5fz9gtkOzsYgQFhHCImLSsLxmPWTC47KxiJg3LEurNBurvDm/AoVFxNRhuQ3vmvaIbCwiZg/L0rph5sOxsYgoEJal9aXJj8XGIqJGWJbWB/MfSJmNpa1NiaNwFRJRKSxL66zKIRTbWIu3Vejx612Fy7ZV68G9YxFRMqwFl1a5R666sZZqq+LDFr4KF2mr6GPWfsdq31bdB/x9Op2un1bWsrDSD9UkrLNObTV4lj4/bmgTVo8H6RPWWYORtPny6HMVvld0PG2qOusZ1lmtIXVK6qJtWBfzD6xfUhet3rE+m3xsXas6a76xNrONsHFSF6uEdTHDONsndbFWWJvXT3eRnjaLhrVJz3u1njarh/XewAiW7WkjrG/tj0NGnwmLiOY/x+KnCIsIYREhLCKERYSwiBAWEcIiQlhECIsIYREhLCKERYSwiHjdr81sv7Tkt5dW8KKwPsSkrRd7/Vf1z4TFD3rNLF7xjqWqqTQJS1Vryoalqjm9YC5+3LCodFvBsKyrlaXCUtX8ojNyFS4t11YkLOuqkNCwxoelKs5chUR2weCwrKuihg9uZFiqYuMq5GrsXhgWlnXVwMAhjglLVW2MGqWrkIgBYVlXzQwZ6LNhqaql58fqKiTiqbCsq8aeHO7xsFTV3jMjdhVyy+G2DoZlXXHbkbBUtZRj43YVct+Bth4Oy7pij8fCUtWyHh29q5C9HmrrgbCsK/Y3sDcsVfEQVyGP2blidoVlXfHenh7uh6UqDnAVcsTddXMnLOuK79xu41ZYquK2G4W4Con4Nizrij2+6+TrsFTFfl/W4iok4ouwrCse9bmZj2GpimM+lOMqZJj3bf0vLOuKUf6GpSqet1XkKmSwS1vXsKwrxvrz/xWqiuFchUT8e/0IQ9lYBPz69R96KHg1/UWCWgAAAABJRU5ErkJggg==';

        if ($idUsuario == null) {
            return $imagemDePerfilPadrao;
        }

        $pesquisaImagemDePerfil = DB::table('imagem')
        ->where('idLogin', '=', $idUsuario)->where('tipo', '=', 1)->where('ativo', '=', 1)
        ->orderByDesc('uploadEm')->get(['base64'])->first();

        $imagemDePerfilDoUsuario = $pesquisaImagemDePerfil;

        if($pesquisaImagemDePerfil != false) {
            return $imagemDePerfilDoUsuario->base64;
        } else {
            return $imagemDePerfilPadrao;
        }
    }

    public function mostrarImagemAlteracaoDePerfil($tokenCarregado) {
        $pesquisaToken = new TokenUso();
        $token = $tokenCarregado;

        $idUsuario = $pesquisaToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado']
            );
        } else {
            $imagemDePerfil = $this->carregarImagemDePerfil($idUsuario);

            return response()->json($imagemDePerfil);
        }
    }

    public function mostrarImagemAvatarDePerfil ($tokenCarregado = null) {
        $token = $tokenCarregado;
        $imagemDePerfil = null;

        if ($token == 'null') {
           $imagemDePerfil = $this->carregarImagemDePerfil(null);
        } else {
           $imagemDePerfil = $this->mostrarImagemAlteracaoDePerfil($token);
        }

        return $imagemDePerfil;
    }

    public function mostrarImagemPerfilVisitado($idPessoaCarregado) {
        $procuraPessoa = new PessoaInformacao();
        $idPessoa = $idPessoaCarregado;

        $idUsuario = $procuraPessoa->solicitaIdUsuarioDependeIdPessoa($idPessoa);

        $imagemDePerfil = null;

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado'
            ]);
        } else {
            $imagemDePerfil = $this->carregarImagemDePerfil($idUsuario);

            return response()->json($imagemDePerfil);
        }
    }

    public function excluirImagemDePerfil($tokenCarregado) {
        $pesquisaToken = new TokenUso();
        $token = $tokenCarregado;

        $idUsuario = $pesquisaToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado'
            ]);
        } else {
            $desativaImagemDePerfil = DB::table('imagem')
            ->where('idLogin', '=', $idUsuario)->where('tipo', '=', 1)->where('ativo', '=', 1)
            ->update(
                ['ativo' => 0]
            );

            return response()->json([
                'Status' => 200,
                'Transacao' => 'OK'
            ]);
        }
    }
}
