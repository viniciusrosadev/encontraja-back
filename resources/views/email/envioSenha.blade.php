<html>
<meta charset="utf-8">
    <head>
        <title>Cadastro novo usuário</title>
    </head>
    <body>
        <div class="container">
        <span>Olá {{ $nomePessoa }},</span>
        <br>
        <span>Sua conta foi criada com sucesso no EncontraJa. Para acessar basta inserir suas credenciais:</span>
        <br>
        <span>Login: {{ $emailUsuario }}</span>
        <br>
        <span>Senha: {{ $senhaBase }}</span>
        <br>
        <br>
        <span>EncontraJa - {{ $anoAtual }}</span>
        </div>
    </body>
</html>