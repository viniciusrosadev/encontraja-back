<?php

namespace EncontraJa\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use EncontraJa\Http\Requests\AnuncioServicoRequest;
use EncontraJa\Http\Controllers\TokenUso;
use EncontraJa\Http\Controllers\LocalizacaoAnuncio;
use EncontraJa\Http\Controllers\PessoaInformacao;
use EncontraJa\Http\Controllers\VisitasAnuncio;
use EncontraJa\Http\Controllers\Imagem;
use EncontraJa\Http\Controllers\Auxiliares;

class AnuncioServico extends Controller
{
    public function cadastraAnuncio(AnuncioServicoRequest $request)
    {
        $operacaoAnuncio = $request->all();
        $token = $operacaoAnuncio['token'];

        $procuraToken = new TokenUso();
        $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado'
            ]);
        } else {
            $localizacaoAnuncio = new LocalizacaoAnuncio();
            $procuraPessoa = new PessoaInformacao();

            $nivelPrioridade = 2;
            $ativo = 1;
            $idLogin = $idUsuario;
            $idPessoa = $procuraPessoa->solicitaIdPessoaDependeIdUsuario($idUsuario);
            $tituloAnuncio = $operacaoAnuncio['tituloAnuncio'];

            if (is_null ($operacaoAnuncio['exibirData']) == true) {
                $exibirData = Carbon::now()->format('Y-m-d');
            } else {
                $exibirData = Carbon::createFromFormat('d/m/Y', $operacaoAnuncio['exibirData']);
            }

            $descricao = $operacaoAnuncio['descricao'];
            $telefone = null;
            $emailContato = null;

            if (isset($operacaoAnuncio['telefone'])) {
                $telefone = $operacaoAnuncio['telefone'];
            }

            if (isset($operacaoAnuncio['emailContato'])) {
                $emailContato = $operacaoAnuncio['emailContato'];
            } 

            $cidadesAnuncio = $operacaoAnuncio['cidadesAnuncio'];

            $inserirAnuncio = DB::table('anuncioServico')->insert([
                'idLogin' => $idLogin,
                'idPessoa' => $idPessoa,
                'tituloAnuncio' => $tituloAnuncio,
                'exibirData' => $exibirData,
                'ativo' => $ativo,
                'nivelPrioridade' => $nivelPrioridade,
                'descricao' => $descricao,
                'telefone' => $telefone,
                'emailContato' => $emailContato
            ]);

            if ($inserirAnuncio == true) {
                $procuraAnuncioInserido = DB::table('anuncioServico')
                ->where('idLogin', '=', $idLogin)->orderBy('criadoEm', 'desc')
                ->get(['id', 'criadoEm'])->first();

                $idAnuncio = $procuraAnuncioInserido->id;

                $inserirLocalizacaoAnuncio = $localizacaoAnuncio->salvarLocalizacaoAnuncio($idAnuncio, $idLogin, $cidadesAnuncio);

            if ($inserirLocalizacaoAnuncio == true) {
                $emailEnvio = new EmailNotificacao;

                $emailPessoa = $procuraPessoa->recuperaEmailPessoa($idPessoa);

                $avisoCadastroDeAnuncio = $emailEnvio->avisoCadastroDeAnuncio($emailPessoa, $idAnuncio, $tituloAnuncio, $descricao);

                return response()->json([
                    'status' => 200,
                    'mensagem' => 'confirmado',
                    'idAnuncio' => $idAnuncio
                ]);
            } else {
                return response()->json([
                    'erro' => 'Não foi possível completar a inserção das cidades'
                ]);
            }
            } else {
                return response()->json([
                    'erro' => 'Não foi possível inserir o anúncio'
                ]);
            }
        }
    }

    public function carregaAnuncio($id, $tokenCarregada = null)
    {
        $idAnuncio = $id;
        $token = $tokenCarregada;

        $consultaAnuncio = DB::table('anuncioServico')
            ->join('pessoaInformacao', 'anuncioServico.idPessoa', '=', 'pessoaInformacao.id')
            ->where('anuncioServico.id', '=', $idAnuncio)->get([
                'anuncioServico.idPessoa',
                'anuncioServico.tituloAnuncio',
                'anuncioServico.ativo',
                'anuncioServico.nivelPrioridade',
                'anuncioServico.descricao',
                'anuncioServico.telefone',
                'anuncioServico.emailContato',
                'anuncioServico.criadoEm',
                'pessoaInformacao.nomePessoa',
                'pessoaInformacao.classificacao'
            ])->first();

        $trataAnuncio = $consultaAnuncio;
        $auxNivelPrioridade = null;
        $auxCriadoEm =  Carbon::createFromFormat('Y-m-d H:i:s', $trataAnuncio->criadoEm)->format('d/m/Y');

        switch ($trataAnuncio->nivelPrioridade) {
            case 1:
                $auxNivelPrioridade = 'Elevado';
                break;
            default:case 2:
                $auxNivelPrioridade = 'Normal';
            break;
            case 3:
                $auxNivelPrioridade = "Baixo";
                break;
        }

        $anuncio = [
            "idPessoa" => $trataAnuncio->idPessoa,
            "tituloAnuncio" => $trataAnuncio->tituloAnuncio,
            "ativo" => $trataAnuncio->ativo,
            "nivelPrioridade" => $auxNivelPrioridade,
            "descricao" => $trataAnuncio->descricao,
            "telefone" => $trataAnuncio->telefone,
            "emailContato" => $trataAnuncio->emailContato,
            "criadoEm" => $auxCriadoEm,
            "nomePessoa" => $trataAnuncio->nomePessoa,
            "classificacao"=> $trataAnuncio->classificacao
        ];

        $idUsuario = null;
        $adicionaVisitasAoAnuncio = new VisitasAnuncio();

        if ($token != null) {
            $procuraToken = new TokenUso();
            $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);
        }

        $inserirVisitasAnuncio = $adicionaVisitasAoAnuncio->adicionaAcessoAoAnuncio($idAnuncio, $idUsuario);

        if ($inserirVisitasAnuncio == false) {
            return response()->json([
                'status' => 'Erro ao inserir visita ao anúncio'
            ]);
        }

        switch (!empty($consultaAnuncio)) {
            case true:
                return response()->json($anuncio);
                break;
            case false:
                return response()->json([
                    'status' => 'Anuncio não encontrado'
                ]);
                break;
            default:
                return response()->json([
                    'status' => 'Erro ao consultar anuncio'
                ]);
        }
    }

    public function carregaAnunciosUsuario($token)
    {
        $procuraToken = new TokenUso();
        $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
            'erro' => 'Usuário não localizado'
            ]);
        } else {
            $anunciosUsuarioJson = new Collection();

            $anunciosUsuario = DB::table('anuncioServico')
            ->where('idLogin', '=', $idUsuario)->where('ativo', '=', 1)
            ->limit(100)
            ->get([
                'anuncioServico.id',
                'anuncioServico.tituloAnuncio',
                'anuncioServico.exibirData',
                'anuncioServico.ativo',
                'anuncioServico.nivelPrioridade',
                'anuncioServico.criadoEm',
            ]);

            $auxiliares = new Auxiliares();
            $procuraImagemAmostragem = new Imagem();

            foreach ($anunciosUsuario as $anuncio) {
                $auxNivelPrioridade = null;
                $auxCriadoEm = null;

                switch ($anuncio->nivelPrioridade) {
                    case 1:
                        $auxNivelPrioridade = 'Elevado';
                        break;
                    default:case 2:
                        $auxNivelPrioridade = 'Normal';
                        break;
                    case 3:
                        $auxNivelPrioridade = "Baixo";
                        break;
                }

                $base64 = $procuraImagemAmostragem->carregarImagemAmostragem($anuncio->id);
                $auxCriadoEm = $auxiliares->normalizaDataCriadoEm($anuncio->criadoEm);

                $anunciosUsuarioJson->add([
                    'idAnuncio' => $anuncio->id,
                    "tituloAnuncio" => $anuncio->tituloAnuncio,
                    "exibirData" => $anuncio->exibirData,
                    "ativo" => $anuncio->ativo,
                    "nivelPrioridade" => $auxNivelPrioridade,
                    'base64' => $base64,
                    "criadoEm" => $auxCriadoEm
                ]);
            }

            return response()->json($anunciosUsuarioJson);
        }
    }

    public function validaIdAnuncio($idAnuncio) {
        $pesquisaIdAnuncio = DB::table('anuncioServico')->where('id', '=', $idAnuncio)->get(['id'])->first();

        if ($pesquisaIdAnuncio == true) {
            return $pesquisaIdAnuncio->id;
        } else {
            return false;
        }
    }

    public function listaAnunciosTopo($quantidadeAnuncios) {
        $procuraAnuncios = DB::table('anuncioServico')
        ->where('ativo', '=', 1)->inRandomOrder()->
        limit($quantidadeAnuncios)->orderBy('criadoEm','DESC')
        ->get();

        if ($procuraAnuncios->isNotEmpty()){
            $idVisitasAnuncioColecao = new Collection();
            $idVisitasAnuncioColecaoOrdenado = new Collection();
            $anunciosColecao = new Collection();
            $procuraVisitasAnuncio = new VisitasAnuncio();

            foreach ($procuraAnuncios as $anuncio) {
                $idVisitasAnuncioColecao->add([
                    'idAnuncio' => $anuncio->id
                ]);
            }

            $idVisitasAnuncioColecao->map(function ($anuncio) use ($procuraVisitasAnuncio, $idVisitasAnuncioColecaoOrdenado) {
                $visitasAnuncio = $procuraVisitasAnuncio->contarVisitasDoAnuncio($anuncio['idAnuncio']);

                $idVisitasAnuncioColecaoOrdenado->add([
                    'quantidadeVisitas' =>  $visitasAnuncio,
                    'idAnuncio' => $anuncio['idAnuncio']
                ]);
            });

            unset($idVisitasAnuncioColecao);

            $procuraImagemAmostragem = new Imagem();
            $auxiliares = new Auxiliares();

            $procuraAnuncios->map(function ($anuncio) use ($idVisitasAnuncioColecaoOrdenado, $anunciosColecao, $procuraImagemAmostragem, $auxiliares) {
                $encontraAnuncioColecao = $idVisitasAnuncioColecaoOrdenado->where('idAnuncio', '=', $anuncio->id)->first();
                $auxCriadoEm = $auxiliares->normalizaDataCriadoEm($anuncio->criadoEm);
                $base64 = $procuraImagemAmostragem->carregarImagemAmostragem($anuncio->id);

                $anunciosColecao->add([
                    'idAnuncio' => $anuncio->id,
                    'tituloAnuncio' => $anuncio->tituloAnuncio,
                    'exibirData' => $anuncio->exibirData,
                    'nivelPrioridade' => $anuncio->nivelPrioridade,
                    'descricao' => $anuncio->descricao,
                    'criadoEm' => $auxCriadoEm,
                    'base64' => $base64,
                    'visitas' => $encontraAnuncioColecao["quantidadeVisitas"]
                ]);
            });

            unset ($procuraAnuncios);

            $ordenarVisitas = $anunciosColecao->sortByDesc('visitas');
            $anunciosColecao = $ordenarVisitas->values()->all();

            unset ($ordenarVisitas);

            return response()->json($anunciosColecao);
        }
    }

    public function listaAnunciosRecentes($quantidadeAnunciosRecentes) {
        $hoje = Carbon::now()->format('Y-m-d');
        $hojeHoraInicial = $hoje. ' 00:00:00';
        $hojeHoraFinal = $hoje. ' 23:59:59';

        $procuraAnuncios = DB::table('anuncioServico')
        ->where('ativo', '=', 1)->whereBetween('exibirData', [$hojeHoraInicial, $hojeHoraFinal])
        ->limit($quantidadeAnunciosRecentes)->orderBy('criadoEm','DESC')
        ->get();

        if ($procuraAnuncios->isNotEmpty()) {
            $pesquisaImagemDePerfil = new Imagem();
            $colecaoAnunciosRecentes = new Collection();
            $auxiliares = new Auxiliares();

            $procuraAnuncios->map(function ($anuncio) use ($pesquisaImagemDePerfil, $colecaoAnunciosRecentes, $auxiliares) {
                $imagemDePerfil = $pesquisaImagemDePerfil->carregarImagemDePerfil($anuncio->idLogin);
                $auxCriadoEm = $auxiliares->normalizaDataCriadoEm($anuncio->criadoEm);

                $colecaoAnunciosRecentes->add([
                    'idAnuncio' => $anuncio->id,
                    'tituloAnuncio' => $anuncio->tituloAnuncio,
                    'criadoEm' =>  $auxCriadoEm,
                    'descricao' => $anuncio->descricao,
                    'imagemPerfil64' => $imagemDePerfil,
                ]);
            });

            return response()->json($colecaoAnunciosRecentes);
        } else {
            return response()->json([
                'erro' => 'Anúncios recentes não encontrados'
            ]);
        }
    }

    public function excluirAnuncio ($tokenCarregado, $idAnuncioCarregado) {
        $token = $tokenCarregado;

        $procuraToken = new TokenUso();
        $idUsuario = $procuraToken->tokenIdentificaIdUsuario($token);

        if (empty($idUsuario)) {
            return response()->json([
                'erro' => 'Usuário não encontrado'
            ]);
        } else {
            $idAnuncio = $this->validaIdAnuncio($idAnuncioCarregado);

            $desativaAnuncio = DB::table('anuncioServico')
            ->where('idLogin', '=', $idUsuario)->where('id', '=', $idAnuncio)
            ->update(['ativo' => 0]);

            return response()->json([
                'Status' => 200,
                'Transacao' => 'OK'
            ]);
        }
    }

    public function arrumaPesquisa($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada) {
        $procuraAnuncios = DB::table('anuncioServico')
        ->join('localizacaoAnuncio', 'anuncioServico.id', '=', 'localizacaoAnuncio.idAnuncio')
        ->join('locCidades', 'localizacaoAnuncio.idCidade', '=', 'locCidades.id')->distinct('anuncioServico.id')
        ->where('anuncioServico.ativo', '=', 1)->where('anuncioServico.tituloAnuncio', 'like', '%'.$termoPesquisado.'%');

        $filtrarData = false;

        if (is_null($dataInicialPesquisada) == false && is_null($dataFinalPesquisada) == false) {
            $filtrarData = true;
        }

        if (is_null($cidadePesquisada) == false)  {
            $procuraAnuncios->where('locCidades.id', '=' , $cidadePesquisada);
        }

        if (is_null($descricaoPesquisada) == false) {
            $procuraAnuncios->where('anuncioServico.descricao', 'like' , '%'.$descricaoPesquisada.'%');
        }

       if ($filtrarData == true) {
            if (is_null($dataInicialPesquisada) == false && is_null($dataFinalPesquisada) == true) {
                $carbonDataInicialPesquisada = Carbon::createFromFormat('Y-m-d', $dataInicialPesquisada);
                if ($carbonDataInicialPesquisada != Carbon::now()->format('Y-m-d')) {
                    $dataFinalPesquisada = Carbon::createFromFormat('Y-m-d', $carbonDataInicialPesquisada)->addDays(15)->format('Y-m-d');
                }
            } else if (is_null($dataInicialPesquisada) == true && is_null($dataFinalPesquisada) == false) {
                $carbonDataFinalPesquisada = Carbon::createFromFormat('Y-m-d', $dataFinalPesquisada);
                if ($carbonDataFinalPesquisada != Carbon::now()->format('Y-m-d')) {
                    $dataInicialPesquisada = Carbon::createFromFormat('Y-m-d', $carbonDataFinalPesquisada)->subDay(15)->format('Y-m-d');
                }
            }

            $procuraAnuncios->whereBetween('anuncioServico.exibirData', [$dataInicialPesquisada, $dataFinalPesquisada]);
        }

        return $procuraAnuncios;
    }

    public function anunciosMaisVisitadosERecentes($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada, $quantidadeAnuncios) {
        $resultadoFinal = new Collection();
        $resultadoVisitas = new Collection();

        $procuraAnuncios = $this->arrumaPesquisa($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada);

        $resultadoProcuraAnuncios = $procuraAnuncios->get([
            'anuncioServico.id',
            'anuncioServico.tituloAnuncio',
            'anuncioServico.descricao',
            'anuncioServico.criadoEm']);

        $procuraVisitasAnuncio = new VisitasAnuncio();

        $resultadoProcuraAnuncios->map(function ($anuncio) use ($procuraVisitasAnuncio, $resultadoVisitas){
            $anuncioVisitas = $procuraVisitasAnuncio->contarVisitasDoAnuncio($anuncio->id);

            $resultadoVisitas->add([
                'idAnuncio' => $anuncio->id,
                'tituloAnuncio' => $anuncio->tituloAnuncio,
                'descricao' => $anuncio->descricao,
                'criadoEm' => $anuncio->criadoEm,
                'visitas' => $anuncioVisitas
            ]);
        });

        $procuraImagemAmostragem = new Imagem();
        $auxiliares = new Auxiliares();

        $resultadoVisitas->sortByDesc('visitas')->take(2)->map(function ($anuncio) use ($procuraImagemAmostragem, $resultadoFinal, $auxiliares){
            $imagemDeAmostragem = $procuraImagemAmostragem->carregarImagemAmostragem($anuncio['idAnuncio']);
            $auxCriadoEm = $auxiliares->normalizaDataCriadoEm($anuncio['criadoEm']);

            $resultadoFinal->add([
                'idAnuncio' => $anuncio['idAnuncio'],
                'tituloAnuncio' => $anuncio['tituloAnuncio'],
                'descricao' => $anuncio['descricao'],
                'criadoEm' => $auxCriadoEm,
                'base64' => $imagemDeAmostragem
            ]);
        });

        return $resultadoFinal;
    }

    public function anunciosAleatorios ($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada, $idsAnunciosExistentes) {
        $resultadoFinal = new Collection();
        $procuraAnuncios = $this->arrumaPesquisa($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada);

        $resultadoAnuncios = $procuraAnuncios->whereNotIn('anuncioServico.id', $idsAnunciosExistentes)
        ->inRandomOrder()->limit(2)
        ->get([
            'anuncioServico.id',
            'anuncioServico.tituloAnuncio',
            'anuncioServico.descricao',
            'anuncioServico.criadoEm'
        ]);

        $procuraImagemAmostragem = new Imagem();
        $auxiliares = new Auxiliares();

        $resultadoAnuncios->map(function ($anuncio) use ($resultadoFinal, $procuraImagemAmostragem, $auxiliares) {
            $imagemDeAmostragem = $procuraImagemAmostragem->carregarImagemAmostragem($anuncio->id);
            $auxCriadoEm = $auxiliares->normalizaDataCriadoEm($anuncio->criadoEm);

            $resultadoFinal->add([
                'idAnuncio' => $anuncio->id,
                'tituloAnuncio' => $anuncio->tituloAnuncio,
                'descricao' => $anuncio->descricao,
                'criadoEm' => $auxCriadoEm,
                'base64' => $imagemDeAmostragem
            ]);
        });

        return $resultadoFinal;
    }

    public function AnunciosMenosVisitados ($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada, $idsAnunciosExistentes) {
        $resultadoFinal = new Collection();
        $resultadoVisitas = new Collection();

        $procuraAnuncios = $this->arrumaPesquisa($cidadePesquisada, $termoPesquisado, $descricaoPesquisada, $dataInicialPesquisada, $dataFinalPesquisada);

        $resultadoProcuraAnuncios = $procuraAnuncios->whereNotIn('anuncioServico.id', $idsAnunciosExistentes)
        ->get([
            'anuncioServico.id',
            'anuncioServico.tituloAnuncio',
            'anuncioServico.descricao',
            'anuncioServico.criadoEm'
        ]);

        $procuraVisitasAnuncio = new VisitasAnuncio();

        $resultadoProcuraAnuncios->map(function ($anuncio) use ($procuraVisitasAnuncio, $resultadoVisitas){
            $anuncioVisitas = $procuraVisitasAnuncio->contarVisitasDoAnuncio($anuncio->id);

            $resultadoVisitas->add([
                'idAnuncio' => $anuncio->id,
                'tituloAnuncio' => $anuncio->tituloAnuncio,
                'descricao' => $anuncio->descricao,
                'criadoEm' => $anuncio->criadoEm,
                'visitas' => $anuncioVisitas
            ]);
        });

        $procuraImagemAmostragem = new Imagem();
        $auxiliares = new Auxiliares();

        $resultadoVisitas->sortBy('visitas', false)->take(2)->map(function ($anuncio) use ($procuraImagemAmostragem, $resultadoFinal, $auxiliares){
            $imagemDeAmostragem = $procuraImagemAmostragem->carregarImagemAmostragem($anuncio['idAnuncio']);
            $auxCriadoEm = $auxiliares->normalizaDataCriadoEm($anuncio['criadoEm']);

            $resultadoFinal->add([
                'idAnuncio' => $anuncio['idAnuncio'],
                'tituloAnuncio' => $anuncio['tituloAnuncio'],
                'descricao' => $anuncio['descricao'],
                'criadoEm' => $auxCriadoEm,
                'base64' => $imagemDeAmostragem
            ]);
        });

        return $resultadoFinal;
    }
}
