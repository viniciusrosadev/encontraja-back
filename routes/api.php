<?php

use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/validarLogin/{email},{senha}', 'Auth\LoginController@validarLoginApi');

Route::post('/enviarUsuario', 'PessoaInformacao@cadastraPessoa');
Route::get('/perfilIdUsuario/{token},{idPessoaCarregado}', 'PessoaInformacao@perfilIdUsuario');

Route::post('/alterarEmail', 'ConfiguracoesPainel@alterarEmail');
Route::post('/alterarSenha', 'ConfiguracoesPainel@alterarSenha');

Route::post('/guardarImagem', 'Imagem@guardarImagem');
Route::get('/carregarImagemAnuncio/{idAnuncio}', 'Imagem@carregarImagemAnuncio');
Route::get('/mostrarImagemAlteracaoDePerfil/{tokenCarregado}', 'Imagem@mostrarImagemAlteracaoDePerfil');
Route::get('/mostrarImagemAvatarDePerfil/{tokenCarregado}', 'Imagem@mostrarImagemAvatarDePerfil');
Route::get('/mostrarImagemPerfilVisitado/{idPessoaCarregado}', 'Imagem@mostrarImagemPerfilVisitado');
Route::get('/excluirImagemDePerfil/{tokenCarregado}', 'Imagem@excluirImagemDePerfil');

Route::post('/enviarAnuncio', 'AnuncioServico@cadastraAnuncio');
Route::get('/carregaAnuncio/{id},{token}', 'AnuncioServico@carregaAnuncio');
Route::get('/carregaAnunciosUsuario/{token}', 'AnuncioServico@carregaAnunciosUsuario');
Route::get('/listaAnunciosTopo/{quantidadeAnuncios}', 'AnuncioServico@listaAnunciosTopo');
Route::get('/listaAnunciosRecentes/{quantidadeAnunciosRecentes}', 'AnuncioServico@listaAnunciosRecentes');
Route::get('/excluirAnuncio/{tokenCarregado},{idAnuncioCarregado}', 'AnuncioServico@excluirAnuncio');

Route::post('/cadastraInstituicao', 'InstituicaoInformacao@cadastraInstituicao');

Route::post('/guardaComentario', 'Comentarios@guardarComentario');

Route::post('/guardaClassificacao', 'ClassificacaoUsuario@guardarClassificacao');

Route::get('/localizaEstados/', 'LocEstados@localizaEstados');
Route::get('/localizaMicroRegioes/{id}', 'LocMicroRegioes@localizaMicroRegioes');
Route::get('/localizaCidades/{idEstado},{idMicroRegiao}', 'LocCidades@localizaCidades');

Route::get('/populaLocais', 'Locais@populaLocais');

Route::get('/carregarLocalizacaoAnuncio/{idAnuncio}', 'LocalizacaoAnuncio@carregarLocalizacaoAnuncio');

Route::post('/pesquisaAnuncio', 'PesquisaAnuncio@pesquisaAnuncio');
