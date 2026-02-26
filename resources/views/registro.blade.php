<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Membros</title>
    <style>
        body { font-family: sans-serif; margin: 50px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background: #4a90e2; color: white; border: none; padding: 10px 20px; cursor: pointer; }
        .error-box { background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .success-box { background: #dcfce7; color: #16a34a; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <h1>Cadastro de Membro</h1>
    @if(session('sucesso'))
        <div class="success-box">
            {{ session('sucesso') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <strong>Ops! Algo deu errado:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/cadastrar" method="POST">
        @csrf 
        
        <div class="form-group">
            <label>Nome Completo:</label>
            <input type="text" name="name" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label>E-mail:</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label>CPF:</label>
            <input type="text" name="cpf" value="{{ old('cpf') }}">
        </div>

        <div class="form-group">
            <label>Senha:</label>
            <input type="password" name="password">
        </div>

        <button type="submit">Finalizar Cadastro</button>
    </form>

</body>
</html>