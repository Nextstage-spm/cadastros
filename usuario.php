<?php
include("cadastrar_professor.php"); // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos 'email' e 'senha' foram enviados e não estão vazios
    if (isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['senha']) && !empty($_POST['senha'])) {
        // Captura os valores do formulário
        $email = $_POST['email'];
        $senha = md5($_POST['senha']); // Criptografa a senha com MD5

        try {
            // Prepara a query para inserir os dados na tabela 'professor'
            $query = $conn->prepare("INSERT INTO usuario (email, senha) VALUES (:email, :senha)");

            // Faz o bind dos parâmetros
            $query->bindParam(':email', $email);
            $query->bindParam(':senha', $senha); // Usa a senha criptografada

            // Executa a query e verifica se foi bem-sucedida
            if ($query->execute()) {
                echo "Usuário logado com sucesso. ";
            } else {
                echo "Erro ao fazer login, verifique os dados inseridos.";
            }
        } catch (PDOException $e) {
            // Exibe a mensagem de erro com detalhes da exceção (para debug)
            echo "Erro ao cadastrar o professor: " . $e->getMessage();
        }
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
    body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }


        h1 {
            text-align: center;
        }

    </style>
</head>
<body>

<!-- Formulário para cadastrar professor -->
<form method="post" action="">
    <!-- Campo para inserir o email do professor -->
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <!-- Campo para inserir a senha do professor -->
    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" required>

    <!-- Botão de envio -->
    <button type="submit">Fazer Login</button>
</form>

</body>
</html>
